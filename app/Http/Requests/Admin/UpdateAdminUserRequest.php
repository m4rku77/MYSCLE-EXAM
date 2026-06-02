<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email,' . $this->route('id')],
            'role'          => ['required', 'in:user,trainer,admin'],
            'profile_photo' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png'],
        ];
    }
}