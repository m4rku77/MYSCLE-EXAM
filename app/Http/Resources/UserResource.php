<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            User::ID => $this->resource->getAttribute(User::ID),
            User::USERNAME => $this->resource->getAttribute(User::USERNAME),
            User::FIRST_NAME => $this->resource->getAttribute(User::FIRST_NAME),
            User::LAST_NAME => $this->resource->getAttribute(User::LAST_NAME),
            User::EMAIL => $this->resource->getAttribute(User::EMAIL),
            User::ROLE => $this->resource->getAttribute(User::ROLE),
            User::STATUS => $this->resource->getAttribute(User::STATUS),
            User::PROFILE_IMAGE => $this->resource->getAttribute(User::PROFILE_IMAGE),
            'full_name' => $this->resource->full_name,
            User::CREATED_AT => $this->resource->getAttribute(User::CREATED_AT),
            User::UPDATED_AT => $this->resource->getAttribute(User::UPDATED_AT),
        ];
    }
}
