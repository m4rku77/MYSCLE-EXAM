<?php

declare(strict_types=1);

namespace App\Http\Controllers\TrainerClient;

use App\Http\Requests\CreateTrainerClientRequest;
use App\Http\Requests\UpdateTrainerClientRequest;
use App\Http\Resources\TrainerClientResource;
use App\Repositories\Logic\TrainerClientLogicRepository;
use Illuminate\Http\JsonResponse;

class TrainerClientController 
{
    public function __construct(
        private readonly TrainerClientLogicRepository $logic
    ) {}

    public function index()
    {
        return TrainerClientResource::collection(
            $this->logic->getAll()
        );
    }

    public function show(int $id): TrainerClientResource
    {
        return new TrainerClientResource(
            $this->logic->getById($id)
        );
    }

    public function store(CreateTrainerClientRequest $request): TrainerClientResource
    {
        $relation = $this->logic->create($request->validated());

        return new TrainerClientResource($relation);
    }

    public function update(UpdateTrainerClientRequest $request, int $id): TrainerClientResource
    {
        $relation = $this->logic->update($id, $request->validated());

        return new TrainerClientResource($relation);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->logic->delete($id);

        return response()->json([], 204);
    }
}