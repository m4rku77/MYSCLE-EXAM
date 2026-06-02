<?php

declare(strict_types=1);

namespace App\Repositories\ExerciseLibrary;

use App\Models\ExerciseLibrary;
use Illuminate\Database\Eloquent\Collection;

class ExerciseLibraryLogicRepository
{
    public function __construct(
        private readonly ExerciseLibraryDbRepository $db
    ) {}

    public function getAll(): Collection
    {
        return $this->db->getAll();
    }

    public function create(string $name): ExerciseLibrary
    {
        if (empty(trim($name))) {
            throw new \InvalidArgumentException('Exercise name is required');
        }

        return $this->db->create([
            'name'         => $name,
            'muscle_group' => null,
        ]);
    }
}