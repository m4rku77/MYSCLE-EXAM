<?php

declare(strict_types=1);

namespace App\Repositories\TrainingPlan;

use App\Models\Exercise;
use App\Models\ExerciseSet;
use App\Models\TrainingPlan;
use Illuminate\Database\Eloquent\Collection;

class TrainingPlanLogicRepository
{
    public function __construct(
        private readonly TrainingPlanDbRepository $db
    ) {}

    public function getAll(): Collection
    {
        return $this->db->getAll();
    }

    public function getById(int $id): TrainingPlan
    {
        return $this->db->getById($id);
    }

    public function create(array $data): TrainingPlan
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException('Training plan name is required.');
        }

        $plan = $this->db->create([
            TrainingPlan::USER_ID => auth()->id(),
            TrainingPlan::NAME    => $data['name'],
        ]);

        // Ja ir exercises — izveido tos arī
        foreach ($data['exercises'] ?? [] as $exerciseData) {
            $exercise = Exercise::create([
                'training_plan_id' => $plan->id,
                'name'             => $exerciseData['name'],
            ]);

            foreach ($exerciseData['sets'] ?? [] as $index => $set) {
                ExerciseSet::create([
                    'exercise_id' => $exercise->id,
                    'set_number'  => $index + 1,
                    'reps'        => $set['reps'] ?? 0,
                    'weight'      => $set['weight'] ?? 0,
                ]);
            }
        }

        return $plan->load('exercises.exerciseSets');
    }

    public function update(int $id, array $data): TrainingPlan
    {
        $existing = $this->db->getById($id);

        if (!$existing) {
            throw new \Exception('Training plan not found.');
        }

        if (isset($data['name']) && empty($data['name'])) {
            throw new \InvalidArgumentException('Name cannot be empty.');
        }

        return $this->db->update($id, $data);
    }

    public function toggleFavorite(int $id, bool $isFavorite): TrainingPlan
    {
        return $this->db->updateFavorite($id, $isFavorite);
    }

    public function delete(int $id): void
    {
        $existing = $this->db->getByIdWithoutUserCheck($id);

        if (!$existing) {
            throw new \Exception('Training plan not found.');
        }

        $this->db->delete($id);
    }
}