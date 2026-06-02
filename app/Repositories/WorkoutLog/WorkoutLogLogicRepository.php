<?php

declare(strict_types=1);

namespace App\Repositories\WorkoutLog;

use App\Models\User;
use App\Models\WorkoutLog;
use Illuminate\Database\Eloquent\Collection;

class WorkoutLogLogicRepository
{
    public function __construct(
        private readonly WorkoutLogDbRepository $db
    ) {}

    public function getAllForUser(int $userId): Collection
    {
        return $this->db->getAllForUser($userId);
    }

    public function start(int $userId, int $planId): WorkoutLog
    {
        return $this->db->create([
            'user_id'          => $userId,
            'training_plan_id' => $planId,
        ]);
    }

    public function finish(int $logId, int $userId, int $duration, array $sets): void
    {
        $log = $this->db->findForUser($logId, $userId);

        $this->db->updateDuration($log, $duration);

        foreach ($sets as $set) {
            $this->db->createSet($log->id, $set);
        }

        User::find($userId)->increment('completed_workouts');
    }
}