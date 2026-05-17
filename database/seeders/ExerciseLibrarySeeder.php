<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExerciseLibrarySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('exercise_library')->insert([

            // CHEST
            ['name' => 'Bench Press', 'muscle_group' => 'Chest', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Incline Bench Press', 'muscle_group' => 'Chest', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Decline Bench Press', 'muscle_group' => 'Chest', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chest Fly', 'muscle_group' => 'Chest', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Push Ups', 'muscle_group' => 'Chest', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cable Crossover', 'muscle_group' => 'Chest', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],

            // BACK
            ['name' => 'Deadlift', 'muscle_group' => 'Back', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pull Ups', 'muscle_group' => 'Back', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Chin Ups', 'muscle_group' => 'Back', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lat Pulldown', 'muscle_group' => 'Back', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Seated Row', 'muscle_group' => 'Back', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bent Over Row', 'muscle_group' => 'Back', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'T-Bar Row', 'muscle_group' => 'Back', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],

            // LEGS
            ['name' => 'Squat', 'muscle_group' => 'Legs', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Leg Press', 'muscle_group' => 'Legs', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lunges', 'muscle_group' => 'Legs', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bulgarian Split Squat', 'muscle_group' => 'Legs', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Leg Extension', 'muscle_group' => 'Legs', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Hamstring Curl', 'muscle_group' => 'Legs', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Calf Raises', 'muscle_group' => 'Legs', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],

            // SHOULDERS
            ['name' => 'Shoulder Press', 'muscle_group' => 'Shoulders', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Arnold Press', 'muscle_group' => 'Shoulders', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Lateral Raise', 'muscle_group' => 'Shoulders', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Front Raise', 'muscle_group' => 'Shoulders', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rear Delt Fly', 'muscle_group' => 'Shoulders', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Face Pull', 'muscle_group' => 'Shoulders', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],

            // ARMS
            ['name' => 'Bicep Curl', 'muscle_group' => 'Arms', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Hammer Curl', 'muscle_group' => 'Arms', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Preacher Curl', 'muscle_group' => 'Arms', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tricep Extension', 'muscle_group' => 'Arms', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tricep Pushdown', 'muscle_group' => 'Arms', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Dips', 'muscle_group' => 'Arms', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Skull Crushers', 'muscle_group' => 'Arms', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],

            // CORE
            ['name' => 'Plank', 'muscle_group' => 'Core', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sit Ups', 'muscle_group' => 'Core', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Leg Raises', 'muscle_group' => 'Core', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Russian Twist', 'muscle_group' => 'Core', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Mountain Climbers', 'muscle_group' => 'Core', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],

            // FULL BODY
            ['name' => 'Burpees', 'muscle_group' => 'Full Body', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kettlebell Swing', 'muscle_group' => 'Full Body', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Clean and Press', 'muscle_group' => 'Full Body', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Farmer Walk', 'muscle_group' => 'Full Body', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Box Jumps', 'muscle_group' => 'Full Body', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],

            // CARDIO
            ['name' => 'Running', 'muscle_group' => 'Cardio', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Cycling', 'muscle_group' => 'Cardio', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Jump Rope', 'muscle_group' => 'Cardio', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rowing Machine', 'muscle_group' => 'Cardio', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Stair Climber', 'muscle_group' => 'Cardio', 'user_id' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
