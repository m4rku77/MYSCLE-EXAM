<?php

declare(strict_types=1);

namespace App\Http\Resources\Exercise;

use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
    public function toArray($request)
{
    return [
        'id' => $this->id,
        'name' => $this->name,

      
        'sets' => $this->exerciseSets->map(function ($set) {
            return [
                'id' => $set->id,
                'reps' => $set->reps,
                'weight' => $set->weight,
            ];
        }),
            
    ];
}
}
