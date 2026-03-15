<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\TrainingPlan;

class TrainingPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            TrainingPlan::ID => $this->resource->getAttribute(TrainingPlan::ID),
            TrainingPlan::USER_ID => $this->resource->getAttribute(TrainingPlan::USER_ID),
            TrainingPlan::NAME => $this->resource->getAttribute(TrainingPlan::NAME),
            TrainingPlan::IS_FAVORITE => $this->resource->getAttribute(TrainingPlan::IS_FAVORITE),
            TrainingPlan::CREATED_AT => $this->resource->getAttribute(TrainingPlan::CREATED_AT),
            TrainingPlan::UPDATED_AT => $this->resource->getAttribute(TrainingPlan::UPDATED_AT),
        ];
    }
}