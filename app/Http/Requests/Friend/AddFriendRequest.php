<?php

declare(strict_types=1);

namespace App\Http\Requests\Friend;

use Illuminate\Foundation\Http\FormRequest;

class AddFriendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'friend_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}