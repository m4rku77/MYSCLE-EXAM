<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateExerciseRequest;
use App\Http\Requests\UpdateExerciseRequest;
use App\Http\Resources\ExerciseResource;
use App\Repositories\Logic\ExerciseLogicRepository;
use Illuminate\Http\JsonResponse;

class ExerciseController extends Controller
{
    public function __construct(
        private readonly ExerciseLogicRepository $logic
    ) {}

    public function index()
    {
        return ExerciseResource::collection(
            $this->logic->getAll()
        );
    }

    public function show(int $id): ExerciseResource
    {
        return new ExerciseResource(
            $this->logic->getById($id)
        );
    }

    public function store(CreateExerciseRequest $request): ExerciseResource
    {
        $exercise = $this->logic->create($request->validated());

        return new ExerciseResource($exercise);
    }

    public function update(UpdateExerciseRequest $request, int $id): ExerciseResource
    {
        $exercise = $this->logic->update($id, $request->validated());

        return new ExerciseResource($exercise);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->logic->delete($id);

        return response()->json([], 204);
    }
}