<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminUser;

use App\Http\Controllers\Controller;
use App\Repositories\User\UserLogicRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\UpdateAdminUserRequest;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly UserLogicRepository $logic
    ) {}

    private function checkAdmin(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }

    // GET /admin/users
    public function index(): JsonResponse
    {
        $this->checkAdmin();
        return response()->json($this->logic->getAll());
    }

    // PUT /admin/users/{id}

public function update(UpdateAdminUserRequest $request, int $id): JsonResponse
{
    $this->checkAdmin();

    $user = $this->logic->getById($id);

    $data = $request->validated();

    if ($request->hasFile('profile_photo')) {
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }
        $data['profile_photo'] = $request->file('profile_photo')->store('profiles', 'public');
    }

    return response()->json($this->logic->update($user, $data));
}

    // GET /admin/trainer-clients
    public function trainerClients(): JsonResponse
    {
        $this->checkAdmin();

        $relations = \App\Models\TrainerClient::with(['trainer', 'client'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($relations);
    }

    // GET /admin/workout-logs
    public function workoutLogs(): JsonResponse
    {
        $this->checkAdmin();

        return response()->json(
            \App\Models\WorkoutLog::with('user')
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    // GET /admin/subscriptions
    public function subscriptions(): JsonResponse
    {
        $this->checkAdmin();

        return response()->json(
            \App\Models\Subscription::with('user')
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    // GET /admin/training-plans
    public function trainingPlans(): JsonResponse
    {
        $this->checkAdmin();

        return response()->json(
            \App\Models\TrainingPlan::with('user')
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    // GET /admin/friends
    public function friends(): JsonResponse
    {
        $this->checkAdmin();

        return response()->json(
            \DB::table('friends')
                ->join('users as u1', 'friends.user_id', '=', 'u1.id')
                ->join('users as u2', 'friends.friend_id', '=', 'u2.id')
                ->select(
                    'friends.id', 'friends.created_at', 'friends.status',
                    'u1.name as user_name', 'u1.email as user_email',
                    'u1.profile_photo as user_photo', 'u2.name as friend_name'
                )
                ->orderBy('friends.created_at', 'desc')
                ->get()
        );
    }
}