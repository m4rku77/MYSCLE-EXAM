<?php

declare(strict_types=1);

namespace App\Http\Requests\Message;

use App\Models\Message;
use Illuminate\Foundation\Http\FormRequest;

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
