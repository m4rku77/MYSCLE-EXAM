<?php

declare(strict_types=1);

namespace App\Repositories\TrainingPlan;

use App\Models\TrainingPlan;
use Illuminate\Database\Eloquent\Collection;

class TrainingPlanDbRepository
{
    public function getAll(): Collection
    {
        return TrainingPlan::with('exercises.sets')
            ->where('user_id', auth()->id())
            ->get();
    }

    public function getById(int $id): TrainingPlan
    {
        return TrainingPlan::with('exercises.sets')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    }

    public function create(array $data): TrainingPlan
    {
        return TrainingPlan::create($data);
    }

    public function update(int $id, array $data): TrainingPlan
    {
        $plan = TrainingPlan::findOrFail($id);
        $plan->update($data);
        return $plan->fresh();
    }

    public function delete(int $id): void
    {
        TrainingPlan::findOrFail($id)->delete();
    }

    public function updateFavorite(int $id, bool $isFavorite): TrainingPlan
    {
        $plan = TrainingPlan::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        $plan->update([TrainingPlan::IS_FAVORITE => $isFavorite]);
        return $plan->fresh();
    }
}