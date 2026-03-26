<?php

declare(strict_types=1);

namespace App\Repositories\TrainingArchive;

use App\Models\TrainingArchive;
use Illuminate\Database\Eloquent\Collection;

class TrainingArchiveLogicRepository
{
    public function __construct(
        private readonly TrainingArchiveDbRepository $db
    ) {}

    public function getAll(): Collection
    {
        return $this->db->getAll();
    }

    public function getById(int $id): TrainingArchive
    {
        return $this->db->getById($id);
    }

    public function create(array $data): TrainingArchive
    {
        return $this->db->create($data);
    }

    public function update(int $id, array $data): TrainingArchive
    {
        return $this->db->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->db->delete($id);
    }
}
