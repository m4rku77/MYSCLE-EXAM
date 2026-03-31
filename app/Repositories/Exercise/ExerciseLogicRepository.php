<?php

declare(strict_types=1);

namespace App\Repositories\Exercise;

use App\Models\Exercise;

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

    public function updateExercise($id, $data)
    {
        $exercise = Exercise::findOrFail($id);

        if (isset($data['name'])) {
            $exercise->name = $data['name'];
        }

        $exercise->save();

        if (isset($data['sets_data'])) {

            $exercise->sets()->delete();

            foreach ($data['sets_data'] as $index => $set) {

                if (! isset($set['reps']) || ! isset($set['weight'])) {
                    continue;
                }

                $exercise->sets()->create([
                    'set_number' => $index + 1,
                    'reps' => (int) $set['reps'],
                    'weight' => (float) $set['weight'],
                ]);
            }
        }

        return $exercise->load('sets');
    }

    public function delete(int $id): void
    {
        $this->dbRepository->delete($id);
    }
}
