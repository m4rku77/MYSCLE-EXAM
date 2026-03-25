<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserLogicRepository
{
    public function __construct(
        private readonly UserDbRepository $db
    ) {}

    public function register(array $data): User
    {
        $data[User::PASSWORD] = Hash::make($data[User::PASSWORD]);

        return $this->db->create($data);
    }

    public function login(array $data): array
    {
        $user = $this->db->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}