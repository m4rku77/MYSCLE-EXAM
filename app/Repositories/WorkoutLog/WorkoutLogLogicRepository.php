<?php

declare(strict_types=1);

namespace App\Repositories\WorkoutLog;

use App\Models\User;
use App\Models\WorkoutLog;
use Illuminate\Database\Eloquent\Collection;
use App\Models\WorkoutSet;

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

    public function update(int $id, ?int $durationSeconds, array $sets): void
    {
        $log = WorkoutLog::where('id', $id)->firstOrFail();

        if ($durationSeconds !== null) {
            $log->update(['duration_seconds' => $durationSeconds]);
        }

        foreach ($sets as $setData) {
            WorkoutSet::where('id', $setData['id'])->update([
                'reps'   => $setData['reps'],
                'weight' => $setData['weight'],
            ]);
        }
    }

    public function delete(int $id): void
    {
        WorkoutLog::where('id', $id)->delete();
    }
}