<?php

declare(strict_types=1);

namespace App\Http\Resources\Subscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Subscription;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            Subscription::ID => $this->resource->getAttribute(Subscription::ID),
            Subscription::USER_ID => $this->resource->getAttribute(Subscription::USER_ID),
            Subscription::TYPE => $this->resource->getAttribute(Subscription::TYPE),
            Subscription::START_DATE => $this->resource->getAttribute(Subscription::START_DATE),
            Subscription::END_DATE => $this->resource->getAttribute(Subscription::END_DATE),
            Subscription::STATUS => $this->resource->getAttribute(Subscription::STATUS),
        ];
    }
}