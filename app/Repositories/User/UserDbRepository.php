<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserDbRepository
{
    public function __construct(
        private readonly User $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?User
    {
        return $this->model->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model
            ->where(User::EMAIL, $email)
            ->first();
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->fresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}