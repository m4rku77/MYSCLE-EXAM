<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TrainingPlanSeeder::class,
            ExerciseSeeder::class,
            ExerciseSetSeeder::class,
            FriendSeeder::class,
            ExerciseLibrarySeeder::class,
        ]);
    }
}
