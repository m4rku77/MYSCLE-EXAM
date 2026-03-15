<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Exercise;

class ExerciseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            Exercise::ID => $this->resource->getAttribute(Exercise::ID),
            Exercise::TRAINING_PLAN_ID => $this->resource->getAttribute(Exercise::TRAINING_PLAN_ID),
            Exercise::NAME => $this->resource->getAttribute(Exercise::NAME),
            Exercise::SETS => $this->resource->getAttribute(Exercise::SETS),
            Exercise::REPS => $this->resource->getAttribute(Exercise::REPS),
            Exercise::WEIGHT => $this->resource->getAttribute(Exercise::WEIGHT),
            Exercise::NOTES => $this->resource->getAttribute(Exercise::NOTES),
            Exercise::CREATED_AT => $this->resource->getAttribute(Exercise::CREATED_AT),
            Exercise::UPDATED_AT => $this->resource->getAttribute(Exercise::UPDATED_AT),
        ];
    }
}