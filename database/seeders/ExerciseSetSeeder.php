<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExerciseSetSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('exercise_sets')->insert([
            [
                'id' => 1,
                'exercise_id' => 1,
                'set_number' => 1,
                'reps' => 10,
                'weight' => 85,
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 2,
                'exercise_id' => 1,
                'set_number' => 2,
                'reps' => 8,
                'weight' => 85,
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 3,
                'exercise_id' => 2,
                'set_number' => 1,
                'reps' => 5,
                'weight' => 100,
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 4,
                'exercise_id' => 4,
                'set_number' => 1,
                'reps' => 10,
                'weight' => 90,
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 5,
                'exercise_id' => 4,
                'set_number' => 2,
                'reps' => 8,
                'weight' => 90,
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 6,
                'exercise_id' => 6,
                'set_number' => 1,
                'reps' => 12,
                'weight' => 20,
                'created_at' => '2026-04-02 09:31:16',
                'updated_at' => '2026-04-02 09:31:16',
            ],
            [
                'id' => 7,
                'exercise_id' => 9,
                'set_number' => 1,
                'reps' => 10,
                'weight' => 25,
                'created_at' => '2026-04-13 07:56:12',
                'updated_at' => '2026-04-13 07:56:12',
            ],
        ]);
    }
}
