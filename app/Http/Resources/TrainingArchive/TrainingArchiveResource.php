<?php

declare(strict_types=1);

namespace App\Http\Resources\TrainingArchive;

use App\Models\TrainingArchive;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingArchiveResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            TrainingArchive::ID => $this->resource->getAttribute(TrainingArchive::ID),
            TrainingArchive::USER_ID => $this->resource->getAttribute(TrainingArchive::USER_ID),
            TrainingArchive::TRAINING_PLAN_ID => $this->resource->getAttribute(TrainingArchive::TRAINING_PLAN_ID),
            TrainingArchive::COMPLETED_AT => $this->resource->getAttribute(TrainingArchive::COMPLETED_AT),
            TrainingArchive::NOTES => $this->resource->getAttribute(TrainingArchive::NOTES),
        ];
    }
}
