<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('exercises')->insert([
            [
                'id' => 1,
                'training_plan_id' => 1,
                'name' => 'Squat',
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 2,
                'training_plan_id' => 1,
                'name' => 'Deadlift',
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 3,
                'training_plan_id' => 2,
                'name' => 'Pull Ups',
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 4,
                'training_plan_id' => 3,
                'name' => 'Bench Press',
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 5,
                'training_plan_id' => 7,
                'name' => 'Bench Press',
                'created_at' => '2026-04-02 09:31:16',
                'updated_at' => '2026-04-02 09:31:16',
            ],
            [
                'id' => 6,
                'training_plan_id' => 8,
                'name' => 'Bicep Curl',
                'created_at' => '2026-04-02 09:31:16',
                'updated_at' => '2026-04-02 09:31:16',
            ],
            [
                'id' => 7,
                'training_plan_id' => 9,
                'name' => 'Chest Press',
                'created_at' => '2026-04-02 09:31:16',
                'updated_at' => '2026-04-02 09:31:16',
            ],
            [
                'id' => 8,
                'training_plan_id' => 10,
                'name' => 'Test Exercise',
                'created_at' => '2026-04-13 07:12:45',
                'updated_at' => '2026-04-13 07:12:45',
            ],
            [
                'id' => 9,
                'training_plan_id' => 11,
                'name' => 'Bicep Curl',
                'created_at' => '2026-04-13 07:56:12',
                'updated_at' => '2026-04-13 07:56:12',
            ],
        ]);
    }
}
