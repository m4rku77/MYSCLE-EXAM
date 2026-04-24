<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::with('trainingPlans.exercises.exerciseSets')
            ->findOrFail($id);

        $workouts = $user->trainingPlans->count();

        $sets = 0;
        $reps = 0;

        foreach ($user->trainingPlans as $plan) {
            foreach ($plan->exercises as $ex) {
                foreach ($ex->exerciseSets as $set) {
                    $sets++;
                    $reps += $set->reps;
                }
            }
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo' => $user->profile_photo
                ? (str_starts_with($user->profile_photo, 'http')
                    ? $user->profile_photo
                    : asset('storage/'.$user->profile_photo))
                : null,
            'created_at' => $user->created_at,
            'location' => $user->location ?? 'Unknown',
            'bio' => $user->bio ?? 'No bio yet',
            'stats' => [
                'workouts' => $workouts,
                'sets' => $sets,
                'reps' => $reps,
            ],
        ]);

    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
