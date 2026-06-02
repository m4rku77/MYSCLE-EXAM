<?php

declare(strict_types=1);

namespace App\Http\Controllers\Friends;

use App\Http\Controllers\Controller;
use App\Http\Requests\Friend\AddFriendRequest;
use App\Repositories\Friend\FriendLogicRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FriendController extends Controller
{
    public function __construct(
        private readonly FriendLogicRepository $logic
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->logic->getFriends(auth()->id()));
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->query('search', '');
        if (!$query) return response()->json([]);
        return response()->json($this->logic->searchUsers(auth()->id(), $query));
    }

    public function add(AddFriendRequest $request): JsonResponse
    {
        try {
            $this->logic->sendRequest(auth()->id(), $request->validated()['friend_id']);
            return response()->json(['message' => 'Friend request sent']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function remove(int $id): JsonResponse
    {
        $this->logic->remove(auth()->id(), $id);
        return response()->json(['message' => 'Friend removed']);
    }

    public function requests(): JsonResponse
    {
        return response()->json($this->logic->getPendingRequests(auth()->id()));
    }

    public function accept(int $id): JsonResponse
    {
        try {
            $this->logic->accept($id, auth()->id());
            return response()->json(['message' => 'Friend added']);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function decline(int $id): JsonResponse
    {
        $this->logic->decline($id, auth()->id());
        return response()->json(['message' => 'Request declined']);
    }

    public function me(): JsonResponse
    {
        $user = auth()->user();
        return response()->json([
            'id' => $user->id, 'name' => $user->name, 'email' => $user->email,
            'profile_photo' => $user->profile_photo, 'goal' => $user->goal,
            'weight' => $user->weight, 'height' => $user->height, 'age' => $user->age,
            'gender' => $user->gender, 'bio' => $user->bio, 'role' => $user->role,
            'completed_workouts' => $user->completed_workouts,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'goal' => ['nullable', 'string', 'max:255'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'height' => ['nullable', 'numeric', 'min:0', 'max:300'],
            'age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'gender' => ['nullable', 'in:male,female,other'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);
        $user = auth()->user();
        $user->update($request->only(['name', 'goal', 'weight', 'height', 'age', 'gender', 'bio']));
        return response()->json(['message' => 'Profile updated', 'user' => $user]);
    }

    public function uploadPhoto(Request $request): JsonResponse
    {
        $request->validate(['photo' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png']]);
        $user = auth()->user();
        $path = $request->file('photo')->store('profiles', 'public');
        $user->profile_photo = $path;
        $user->save();
        return response()->json(['message' => 'Photo uploaded', 'photo' => $path]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user = auth()->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Wrong current password'], 400);
        }
        $user->password = Hash::make($request->new_password);
        $user->save();
        return response()->json(['message' => 'Password updated']);
    }
}