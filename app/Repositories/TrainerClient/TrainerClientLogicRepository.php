<?php

declare(strict_types=1);

namespace App\Repositories\TrainerClient;

use App\Models\TrainerClient;
use Illuminate\Database\Eloquent\Collection;

class TrainerClientLogicRepository
{
    public function __construct(
        private readonly TrainerClientDbRepository $db
    ) {}

    public function getAll(): Collection
    {
        return $this->db->getAll();
    }

    public function getById(int $id): TrainerClient
    {
        return $this->db->getById($id);
    }

    public function create(array $data): TrainerClient
    {
        return $this->db->create($data);
    }

    public function update(int $id, array $data): TrainerClient
    {
        return $this->db->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->db->delete($id);
    }
}
