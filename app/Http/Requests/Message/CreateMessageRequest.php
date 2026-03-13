<?php

declare(strict_types=1);

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Message;

class CreateMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            Message::SENDER_ID => ['required', 'integer', 'exists:users,id'],
            Message::RECEIVER_ID => ['required', 'integer', 'exists:users,id'],
            Message::MESSAGE => ['required', 'string'],
        ];
    }
}