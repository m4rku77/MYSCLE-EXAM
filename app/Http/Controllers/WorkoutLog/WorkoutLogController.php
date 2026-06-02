<?php

declare(strict_types=1);

namespace App\Http\Controllers\WorkoutLog;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkoutLog\CreateWorkoutLogRequest;
use App\Http\Requests\WorkoutLog\FinishWorkoutLogRequest;
use App\Repositories\WorkoutLog\WorkoutLogLogicRepository;
use Illuminate\Http\JsonResponse;

class WorkoutLogController extends Controller
{
    public function __construct(
        private readonly WorkoutLogLogicRepository $logic
    ) {}

    // GET /workout-logs
    public function index(): JsonResponse
    {
        return response()->json(
            $this->logic->getAllForUser(auth()->id())
        );
    }

    // POST /workout-logs/start
    public function start(CreateWorkoutLogRequest $request): JsonResponse
    {
        $log = $this->logic->start(
            auth()->id(),
            $request->validated()['training_plan_id']
        );

        return response()->json($log);
    }

    // POST /workout-logs/{id}/finish
    public function finish(FinishWorkoutLogRequest $request, int $id): JsonResponse
    {
        $this->logic->finish(
            $id,
            auth()->id(),
            $request->validated()['duration_seconds'],
            $request->validated()['sets']
        );

        return response()->json(['message' => 'Workout saved']);
    }
}