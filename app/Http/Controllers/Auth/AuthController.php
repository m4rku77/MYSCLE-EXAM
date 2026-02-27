<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Data\Auth\UpdatePasswordData;
use App\Data\User\CreateUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Models\User;
use App\Repositories\Auth\PasswordLogicRepository;
use App\Repositories\User\UserLogicRepository;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class AuthController extends Controller
{
    public function __construct(
        private readonly UserLogicRepository $userLogic,
        private readonly PasswordLogicRepository $passwordLogic,
    ) {}

    /**
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out',
        ]);
    }

    public function register(RegisterUserRequest $request): Response
    {
        $data = CreateUserData::from($request->validated());

        $user = $this->userLogic->createUser($data);

        event(new Registered($user));

        return response()->noContent();
    }

    public function sendEmailVerification(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json();
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'status' => 'verification-link-sent',
        ]);
    }

    public function verifyEmail(EmailVerificationRequest $request): Response
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->noContent();
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->noContent();
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink([
            User::EMAIL => $request->validated()[User::EMAIL],
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                User::EMAIL => [__($status)],
            ]);
        }

        return response()->json([
            'status' => __($status),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();

        $status = Password::reset(
            [
                User::EMAIL => $data[User::EMAIL],
                User::PASSWORD => $data[User::PASSWORD],
                'password_confirmation' => $data['password_confirmation'],
                'token' => $data['token'],
            ],
            function (User $user) use ($data): void {
                $user->forceFill([
                    User::PASSWORD => bcrypt($data[User::PASSWORD]),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                User::EMAIL => [__($status)],
            ]);
        }

        return response()->json([
            'status' => __($status),
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): Response
    {
        $data = UpdatePasswordData::from($request->validated());

        $this->passwordLogic->update($request->user(), $data);

        return response()->noContent();
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
