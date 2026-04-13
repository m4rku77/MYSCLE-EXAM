<?php

namespace Database\Seeders;

use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Database\Seeder;

class TrainingPlanSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();

        TrainingPlan::create([
            'user_id' => $user->id,
            'name' => 'Pirmdiena - Chest A',
        ]);

        TrainingPlan::create([
            'user_id' => $user->id,
            'name' => 'Otrdiena - Back',
        ]);

        TrainingPlan::create([
            'user_id' => $user->id,
            'name' => 'Trešdiena - Shoulders',
        ]);
    }
}
