<?php

declare(strict_types=1);

namespace App\Repositories\TrainingArchive;

use App\Models\TrainingArchive;
use Illuminate\Database\Eloquent\Collection;

class TrainingArchiveDbRepository
{
    public function __construct(
        private readonly TrainingArchive $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->get();
    }

    public function getById(int $id): TrainingArchive
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): TrainingArchive
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): TrainingArchive
    {
        $archive = $this->getById($id);

        $archive->update($data);

        return $archive->fresh();
    }

    public function delete(int $id): void
    {
        $archive = $this->getById($id);

        $archive->delete();
    }
}