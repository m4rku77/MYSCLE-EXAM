<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exercise;

use App\Http\Resources\ExerciseResource;
use App\Models\Exercise;
use App\Repositories\Exercise\ExerciseLogicRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Exercise\CreateExerciseRequest;
use App\Http\Requests\Exercise\UpdateExerciseRequest;

class ExerciseController
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

        
    public function store(CreateExerciseRequest $request)
    {
        $exercise = $this->logic->create([
            'training_plan_id' => $request->workout_id,
            'name'             => $request->name,
            'notes'            => $request->notes,
        ]);

        return response()->json($exercise);
    }

    public function update(UpdateExerciseRequest $request, $id)
    {
        $this->logic->updateExercise((int) $id, [
            'name'      => $request->name,
            'sets_data' => $request->sets_data,
            'notes'     => $request->notes,
        ]);

        return response()->json(['message' => 'Saved successfully']);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->logic->delete($id);

        return response()->json([], 204);
    }
}