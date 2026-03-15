<?php

declare(strict_types=1);

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Message;

class UpdateMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            Message::MESSAGE => ['sometimes', 'string'],
        ];
    }
}