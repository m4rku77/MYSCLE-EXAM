<?php

namespace App\Http\Controllers\Workouts;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\ExerciseSet;
use App\Models\TrainingPlan;
use App\Models\User;
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

    public function store(Request $request)
    {
        $user = $request->user();

        $workout = TrainingPlan::create([
            'user_id' => $user->id,
            'name' => $request->name,
        ]);

        foreach ($request->exercises as $exerciseData) {

            $exercise = Exercise::create([
                'training_plan_id' => $workout->id,
                'name' => $exerciseData['name'],
                'weight' => 0,
            ]);

            foreach ($exerciseData['sets'] as $setData) {
                ExerciseSet::create([
                    'exercise_id' => $exercise->id,
                    'set_number' => $setData['set_number'],
                    'reps' => $setData['reps'],
                    'weight' => $setData['weight'],
                ]);
            }
        }

        return response()->json([
            'message' => 'Workout created successfully',
        ]);
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
