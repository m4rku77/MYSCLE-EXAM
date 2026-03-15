<?php

declare(strict_types=1);

namespace App\Http\Controllers\TrainingArchive;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingArchive\CreateTrainingArchiveRequest;
use App\Http\Requests\TrainingArchive\UpdateTrainingArchiveRequest;
use App\Http\Resources\TrainingArchive\TrainingArchiveResource;
use App\Repositories\TrainingArchive\TrainingArchiveLogicRepository;
use Illuminate\Http\JsonResponse;

class TrainingArchiveController extends Controller
{
    public function __construct(
        private readonly TrainingArchiveLogicRepository $logic
    ) {}

    public function index()
    {
        return TrainingArchiveResource::collection(
            $this->logic->getAll()
        );
    }

    public function show(int $id): TrainingArchiveResource
    {
        return new TrainingArchiveResource(
            $this->logic->getById($id)
        );
    }

    public function store(CreateTrainingArchiveRequest $request): TrainingArchiveResource
    {
        $archive = $this->logic->create($request->validated());

        return new TrainingArchiveResource($archive);
    }

    public function update(UpdateTrainingArchiveRequest $request, int $id): TrainingArchiveResource
    {
        $archive = $this->logic->update($id, $request->validated());

        return new TrainingArchiveResource($archive);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->logic->delete($id);

        return response()->json([], 204);
    }
}