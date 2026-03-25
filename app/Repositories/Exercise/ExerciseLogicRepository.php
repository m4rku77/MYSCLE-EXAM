<?php

declare(strict_types=1);

namespace App\Repositories\Exercise;

use App\Repositories\Exercise\ExerciseDbRepository;

class ExerciseLogicRepository
{
    public function __construct(
        private readonly ExerciseDbRepository $dbRepository
    ) {}

    public function getAll()
    {
        return $this->dbRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->dbRepository->getById($id);
    }

    public function create(array $data)
    {
        return $this->dbRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->dbRepository->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->dbRepository->delete($id);
    }
}