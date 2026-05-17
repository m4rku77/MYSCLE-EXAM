<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FriendSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('friends')->insert([
            [
                'id' => 1,
                'user_id' => 4,
                'friend_id' => 1,
                'status' => 'accepted',
                'created_at' => '2026-04-02 09:31:16',
                'updated_at' => '2026-04-02 09:31:16',
            ],
            [
                'id' => 2,
                'user_id' => 4,
                'friend_id' => 2,
                'status' => 'accepted',
                'created_at' => '2026-04-02 09:31:16',
                'updated_at' => '2026-04-02 09:31:16',
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'friend_id' => 4,
                'status' => 'accepted',
                'created_at' => '2026-04-02 09:31:16',
                'updated_at' => '2026-04-02 09:31:16',
            ],
        ]);
    }
}
