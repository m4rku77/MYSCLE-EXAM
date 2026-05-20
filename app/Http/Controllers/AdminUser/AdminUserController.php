<?php

namespace App\Http\Controllers\AdminUser;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminUserController
{
    public function index()
    {
        return User::all();
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->hasFile('profile_photo')) {

            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $path = $request->file('profile_photo')->store('profiles', 'public');
            $user->profile_photo = $path;
        }

        $user->save();

        return response()->json($user);
    }

    public function subscriptions()
    {
        $subscriptions = \App\Models\Subscription::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($subscriptions);
    }
    public function workoutLogs()
    {
        $logs = \App\Models\WorkoutLog::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }
    public function trainingPlans()
    {
        $plans = \App\Models\TrainingPlan::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($plans);
    }
    public function trainerClients()
    {
        $relations = \App\Models\TrainerClient::with(['trainer', 'client'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($relations);
    }
    public function friends()
    {
        $friends = \DB::table('friends')
            ->join('users as u1', 'friends.user_id', '=', 'u1.id')
            ->join('users as u2', 'friends.friend_id', '=', 'u2.id')
            ->select(
                'friends.id',
                'friends.created_at',
                'friends.status',
                'u1.name as user_name',
                'u1.email as user_email',
                'u1.profile_photo as user_photo',
                'u2.name as friend_name'
            )
            ->orderBy('friends.created_at', 'desc')
            ->get();

        return response()->json($friends);
    }
}
