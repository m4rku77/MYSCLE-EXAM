<?php

declare(strict_types=1);

namespace App\Repositories\TrainingPlan;

use App\Models\TrainingPlan;

class TrainingPlanDbRepository
{
    public function getAll()
    {
        return TrainingPlan::all();
    }

    public function getById(int $id): TrainingPlan
    {
        return TrainingPlan::findOrFail($id);
    }

    public function create(array $data): TrainingPlan
    {
        return TrainingPlan::create($data);
    }

    public function update(int $id, array $data): TrainingPlan
    {
        $trainingPlan = TrainingPlan::findOrFail($id);

        $trainingPlan->update($data);

        return $trainingPlan;
    }

    public function delete(int $id): void
    {
        $trainingPlan = TrainingPlan::findOrFail($id);

        $trainingPlan->delete();
    }
}
