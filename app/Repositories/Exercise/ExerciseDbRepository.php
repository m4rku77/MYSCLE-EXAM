<?php

declare(strict_types=1);

namespace App\Repositories\Exercise;

use App\Models\Exercise;

class ExerciseDbRepository
{
    public function getAll()
    {
        return Exercise::all();
    }

    public function getById(int $id): Exercise
    {
        return Exercise::findOrFail($id);
    }

    public function create(array $data): Exercise
    {
        return Exercise::create($data);
    }

    public function update(int $id, array $data): Exercise
    {
        $exercise = Exercise::findOrFail($id);

        $exercise->update($data);

        return $exercise;
    }

    public function delete(int $id): void
    {
        $exercise = Exercise::findOrFail($id);

        $exercise->delete();
    }
}