<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exercise;

use App\Http\Requests\CreateExerciseRequest;
use App\Http\Resources\ExerciseResource;
use App\Repositories\Exercise\ExerciseLogicRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function store(CreateExerciseRequest $request): ExerciseResource
    {
        $exercise = $this->logic->create($request->validated());

        return new ExerciseResource($exercise);
    }

    public function update(Request $request, $id)
    {
        $this->logic->updateExercise($id, [
            'name' => $request->name,
            'sets_data' => $request->sets_data,
        ]);

        return response()->json([
            'message' => 'Saved successfully',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->logic->delete($id);

        return response()->json([], 204);
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,

            'sets' => $this->sets->map(function ($set) {
                return [
                    'id' => $set->id,
                    'set_number' => $set->set_number,
                    'reps' => $set->reps,
                    'weight' => $set->weight,
                ];
            }),
        ];
    }
}
