<?php

declare(strict_types=1);

namespace App\Repositories\Auth;

use App\Data\Auth\UpdatePasswordData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class PasswordLogicRepository
{
    public function __construct(
        private readonly PasswordDbRepository $db
    ) {}

    public function update(User $user, UpdatePasswordData $data): void
    {
        $this->db->update($user, [
            User::PASSWORD => Hash::make($data->password),
        ]);
    }
}
