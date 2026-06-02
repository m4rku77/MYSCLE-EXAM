<?php

declare(strict_types=1);

namespace App\Repositories\WorkoutLog;

use App\Models\WorkoutLog;
use App\Models\WorkoutLogSet;
use Illuminate\Database\Eloquent\Collection;

class WorkoutLogDbRepository
{
    public function getAllForUser(int $userId): Collection
    {
        return WorkoutLog::where('user_id', $userId)
            ->with(['sets', 'trainingPlan'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): WorkoutLog
    {
        return WorkoutLog::create($data);
    }

    public function findForUser(int $id, int $userId): WorkoutLog
    {
        return WorkoutLog::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function updateDuration(WorkoutLog $log, int $duration): void
    {
        $log->update(['duration_seconds' => $duration]);
    }

    public function createSet(int $logId, array $set): WorkoutLogSet
    {
        return WorkoutLogSet::create([
            'workout_log_id' => $logId,
            'exercise_name'  => $set['exercise_name'],
            'set_number'     => $set['set_number'],
            'reps'           => $set['reps'],
            'weight'         => $set['weight'],
        ]);
    }
}