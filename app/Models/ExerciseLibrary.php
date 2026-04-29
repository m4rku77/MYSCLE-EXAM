<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseLibrary extends Model
{
    protected $table = 'exercise_library';

    protected $fillable = [
        'name',
        'muscle_group',
    ];
}
