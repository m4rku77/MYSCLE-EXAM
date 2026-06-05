<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workouts;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingPlan\CreateTrainingPlanRequest;
use App\Http\Requests\TrainingPlan\UpdateTrainingPlanRequest;
use App\Http\Resources\TrainingPlan\TrainingPlanResource;
use App\Repositories\TrainingPlan\TrainingPlanLogicRepository;
use Illuminate\Http\JsonResponse;

class WorkoutController extends Controller
{
    public function __construct(
        private readonly TrainingPlanLogicRepository $logic
    ) {}

    // POST /workouts
    public function store(CreateTrainingPlanRequest $request): JsonResponse
    {
        try {
            $plan = $this->logic->create($request->validated());
            return response()->json($plan, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    // PUT /workouts/{id}
    public function update(UpdateTrainingPlanRequest $request, int $id): JsonResponse
    {
        try {
            $plan = $this->logic->update($id, $request->validated());
            return response()->json($plan);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->logic->delete($id);
            return response()->json(['message' => 'Workout deleted']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Workout not found'], 404);
        }
    }
}