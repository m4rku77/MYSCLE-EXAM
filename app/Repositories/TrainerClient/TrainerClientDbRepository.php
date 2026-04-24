<?php

declare(strict_types=1);

namespace App\Repositories\TrainerClient;

use App\Models\TrainerClient;
use Illuminate\Database\Eloquent\Collection;

class TrainerClientDbRepository
{
    public function __construct(
        private readonly TrainerClient $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->get();
    }

    public function getById(int $id): TrainerClient
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): TrainerClient
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): TrainerClient
    {
        $relation = $this->getById($id);

        $relation->update($data);

        return $relation->fresh();
    }

    public function delete(int $id): void
    {
        $relation = $this->getById($id);

        $relation->delete();
    }
}
