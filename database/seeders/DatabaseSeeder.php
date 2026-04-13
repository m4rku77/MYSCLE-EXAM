<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::factory(3)
            ->create()
            ->each(function ($user) {

                TrainingPlan::factory(3)
                    ->create(['user_id' => $user->id])
                    ->each(function ($plan) {

                        Exercise::factory(5)
                            ->create(['training_plan_id' => $plan->id]);

                    });

            });
    }
}
