<?php

namespace App\Http\Controllers\Workouts;

use App\Http\Controllers\Controller;
use App\Models\TrainingPlan;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return TrainingPlan::with('exercises.sets')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($plan) {

                $totalSets = 0;
                $totalReps = 0;

                foreach ($plan->exercises as $ex) {

                    $totalSets += $ex->sets->count();

                    foreach ($ex->sets as $set) {
                        $totalReps += $set->reps;
                    }
                }

                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'sets' => $totalSets,
                    'reps' => $totalReps,
                    'created_at' => $plan->created_at
                ];
            });
    }

    public function show(Request $request, $id)
    {
        return TrainingPlan::with('exercises.sets')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }

    public function track(Request $request, $id)
    {
        return TrainingPlan::with('exercises.sets')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }
}
