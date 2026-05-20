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
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
