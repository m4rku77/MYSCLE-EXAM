<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrainingPlanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('training_plans')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'name' => 'Leg Day',
                'is_favorite' => 0,
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'name' => 'Back Day',
                'is_favorite' => 0,
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'name' => 'Push Day',
                'is_favorite' => 0,
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 4,
                'user_id' => 2,
                'name' => 'Leg Day',
                'is_favorite' => 0,
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 5,
                'user_id' => 2,
                'name' => 'Back Day',
                'is_favorite' => 0,
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 6,
                'user_id' => 2,
                'name' => 'Back Day',
                'is_favorite' => 0,
                'created_at' => '2026-04-02 09:31:15',
                'updated_at' => '2026-04-02 09:31:15',
            ],
            [
                'id' => 7,
                'user_id' => 4,
                'name' => 'Leg Day',
                'is_favorite' => 0,
                'created_at' => '2026-04-02 09:31:16',
                'updated_at' => '2026-04-02 12:32:35',
            ],
            [
                'id' => 8,
                'user_id' => 4,
                'name' => 'Pull Day',
                'is_favorite' => 0,
                'created_at' => '2026-04-02 09:31:16',
                'updated_at' => '2026-04-02 12:32:28',
            ],
            [
                'id' => 9,
                'user_id' => 3,
                'name' => 'Chest Day',
                'is_favorite' => 0,
                'created_at' => '2026-04-02 09:31:16',
                'updated_at' => '2026-04-02 09:31:16',
            ],
            [
                'id' => 10,
                'user_id' => 4,
                'name' => 'as',
                'is_favorite' => 0,
                'created_at' => '2026-04-13 07:12:45',
                'updated_at' => '2026-04-13 07:12:45',
            ],
            [
                'id' => 11,
                'user_id' => 4,
                'name' => 'Bicep day',
                'is_favorite' => 0,
                'created_at' => '2026-04-13 07:56:12',
                'updated_at' => '2026-04-13 07:56:12',
            ],
        ]);
    }
}
