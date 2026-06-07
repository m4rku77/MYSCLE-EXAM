<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutSet extends Model
{
    protected $table = 'workout_log_sets';

    protected $fillable = [
        'workout_log_id',
        'exercise_name',
        'set_number',
        'reps',
        'weight',
    ];

    public function workoutLog(): BelongsTo
    {
        return $this->belongsTo(WorkoutLog::class);
    }
}