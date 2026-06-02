<?php

declare(strict_types=1);

namespace App\Http\Resources\TrainingPlan;

use App\Http\Resources\Exercise\ExerciseResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingPlanResource extends JsonResource
{
public function toArray($request): array
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'is_favorite' => (bool) $this->is_favorite,
        'notes' => $this->notes,
        'created_at' => $this->created_at,

        'exercises_count' => $this->exercises->count(),
        'exercises' => ExerciseResource::collection($this->exercises),

        'sets' => $this->exercises->sum('sets'),
        'reps' => $this->exercises->sum(fn ($ex) => $ex->sets * $ex->reps),
    ];
}
}