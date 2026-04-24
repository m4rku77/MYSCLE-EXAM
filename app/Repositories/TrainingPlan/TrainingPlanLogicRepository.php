<?php

declare(strict_types=1);

namespace App\Repositories\TrainingPlan;

use App\Models\TrainingPlan;

class TrainingPlanLogicRepository
{
    private TrainingPlanDbRepository $db;

    public function __construct(TrainingPlanDbRepository $db)
    {
        $this->db = $db;
    }

    public function getAll()
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

        return $this->db->create($data);
    }

    public function update(int $id, array $data): TrainingPlan
    {
        $existing = $this->db->getById($id);

        if (! $existing) {
            throw new \Exception('Training plan not found.');
        }

        if (isset($data['name']) && empty($data['name'])) {
            throw new \InvalidArgumentException('Name cannot be empty.');
        }

        return $this->db->update($id, $data);
    }

    public function delete(int $id): void
    {
        $existing = $this->db->getById($id);

        if (! $existing) {
            throw new \Exception('Training plan not found.');
        }

        $this->db->delete($id);
    }
}
