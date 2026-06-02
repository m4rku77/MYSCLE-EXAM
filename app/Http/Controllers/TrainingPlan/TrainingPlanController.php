<?php

declare(strict_types=1);

namespace App\Http\Controllers\TrainingPlan;

use App\Http\Controllers\Controller;
use App\Http\Resources\TrainingPlan\TrainingPlanResource;
use App\Repositories\TrainingPlan\TrainingPlanLogicRepository;
use Illuminate\Http\JsonResponse;

class TrainingPlanController extends Controller
{
    public function __construct(
        private readonly TrainingPlanLogicRepository $logic
    ) {}

    // GET /workouts
    public function index(): JsonResponse
    {
        return response()->json(
            $this->logic->getAll()
        );
    }

    // GET /workouts/{id}
    public function show(int $id): JsonResponse
    {
        try {
            return response()->json(
                $this->logic->getById($id)
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Workout not found'], 404);
        }
    }
}