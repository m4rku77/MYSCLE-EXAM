<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutLogSet extends Model
{
    protected $fillable = [
        'workout_log_id',
        'exercise_name',
        'set_number',
        'reps',
        'weight',
    ];
}
