<?php

declare(strict_types=1);

namespace App\Http\Resources\Message;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            Message::ID => $this->resource->getAttribute(Message::ID),
            Message::SENDER_ID => $this->resource->getAttribute(Message::SENDER_ID),
            Message::RECEIVER_ID => $this->resource->getAttribute(Message::RECEIVER_ID),
            Message::MESSAGE => $this->resource->getAttribute(Message::MESSAGE),
            Message::CREATED_AT => $this->resource->getAttribute(Message::CREATED_AT),
        ];
    }
}
