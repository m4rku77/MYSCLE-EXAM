<?php

declare(strict_types=1);

namespace App\Repositories\Auth;

use App\Models\User;

final class PasswordDbRepository
{
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }
}
