<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutLog extends Model
{
    protected $fillable = [
        'user_id',
        'training_plan_id',
        'duration_seconds',
    ];

    public function sets()
    {
        return $this->hasMany(WorkoutLogSet::class);
    }
}
