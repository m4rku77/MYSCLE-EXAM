<?php

declare(strict_types=1);

namespace App\Http\Controllers\TrainingPlan;

use App\Http\Requests\CreateTrainingPlanRequest;
use App\Http\Requests\UpdateTrainingPlanRequest;
use App\Http\Resources\TrainingPlanResource;
use App\Repositories\Logic\TrainingPlanLogicRepository;
use Illuminate\Http\JsonResponse;

class TrainingPlanController
{
    public function __construct(
        private readonly TrainingPlanLogicRepository $logic
    ) {}

    public function index()
    {
        return TrainingPlanResource::collection(
            $this->logic->getAll()
        );
    }

    public function show(int $id): TrainingPlanResource
    {
        return new TrainingPlanResource(
            $this->logic->getById($id)
        );
    }

    public function store(CreateTrainingPlanRequest $request): TrainingPlanResource
    {
        $plan = $this->logic->create($request->validated());

        return new TrainingPlanResource($plan);
    }

    public function update(UpdateTrainingPlanRequest $request, int $id): TrainingPlanResource
    {
        $plan = $this->logic->update($id, $request->validated());

        return new TrainingPlanResource($plan);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->logic->delete($id);

        return response()->json([], 204);
    }
}
