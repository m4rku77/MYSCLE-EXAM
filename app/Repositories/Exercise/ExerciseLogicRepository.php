<?php

declare(strict_types=1);

namespace App\Repositories\Exercise;

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

    

    public function updateExercise(int $id, array $data)
    {
        $exercise = $this->dbRepository->getById($id);

        if (! $exercise) {
            throw new \Exception('Exercise not found');
        }

        $exercise->update([
            'name' => $data['name'] ?? $exercise->name,
            'notes' => $data['notes'] ?? $exercise->notes,
        ]);

        if (! empty($data['sets_data']) && is_array($data['sets_data'])) {

            $exercise->exerciseSets()->delete();

            foreach ($data['sets_data'] as $index => $set) {

                $exercise->exerciseSets()->create([
                    'exercise_id' => $exercise->id,
                    'set_number' => $index + 1,
                    'reps' => $set['reps'] ?? 0,
                    'weight' => $set['weight'] ?? 0,
                ]);
            }
        }

        return $exercise->load('exerciseSets');
    }

    public function delete(int $id): void
    {
        $this->dbRepository->delete($id);
    }
}
