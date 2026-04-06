<?php

namespace App\Http\Controllers\Workouts;

use App\Http\Controllers\Controller;
use App\Models\TrainingPlan;
use Illuminate\Http\Request;

class WorkoutController extends Controller
{
    public function index()
{
    $workouts = TrainingPlan::with('exercises.sets')
        ->where('user_id', auth()->id())
        ->get();

    return $workouts->map(function ($w) {

        $sets = 0;
        $reps = 0;

        foreach ($w->exercises as $ex) {
            foreach ($ex->sets as $set) {
                $sets++;
                $reps += $set->reps;
            }
        }

        return [
            'id' => $w->id,
            'name' => $w->name,
            'exercises_count' => $w->exercises->count(),
            'sets' => $sets,
            'reps' => $reps,
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
