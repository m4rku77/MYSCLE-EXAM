<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachNote extends Model
{
    protected $table = 'coach_notes';

    protected $fillable = [
        'trainer_id',
        'client_id',
        'note',
    ];
}
