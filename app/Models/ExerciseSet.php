<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseSet extends Model
{
    protected $table = 'exercise_sets'; 
    protected $fillable = [
        'exercise_id',
        'set_number',
        'reps',
        'weight',
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
