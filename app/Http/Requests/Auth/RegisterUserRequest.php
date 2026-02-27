<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            User::NAME => ['required', 'string', 'max:255'],
            User::SURNAME => ['required', 'string', 'max:255'],
            User::EMAIL => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            User::PASSWORD => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
