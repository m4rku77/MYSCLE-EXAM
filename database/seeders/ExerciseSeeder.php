<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\TrainingPlan;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run()
    {
        $plans = TrainingPlan::all();

        foreach ($plans as $plan) {
            Exercise::create([
                'training_plan_id' => $plan->id,
                'name' => 'Bench Press',
                'sets' => 4,
                'reps' => 10,
                'weight' => 60,
            ]);

            Exercise::create([
                'training_plan_id' => $plan->id,
                'name' => 'Incline Dumbbell Press',
                'sets' => 3,
                'reps' => 12,
                'weight' => 20,
            ]);
        }
    }
}
