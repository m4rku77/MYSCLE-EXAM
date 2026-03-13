<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\TrainerClient;

class TrainerClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            TrainerClient::ID => $this->resource->getAttribute(TrainerClient::ID),
            TrainerClient::TRAINER_ID => $this->resource->getAttribute(TrainerClient::TRAINER_ID),
            TrainerClient::ATHLETE_ID => $this->resource->getAttribute(TrainerClient::ATHLETE_ID),
            TrainerClient::CREATED_AT => $this->resource->getAttribute(TrainerClient::CREATED_AT),
            TrainerClient::UPDATED_AT => $this->resource->getAttribute(TrainerClient::UPDATED_AT),
        ];
    }
}