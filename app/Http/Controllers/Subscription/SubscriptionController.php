<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\CreateSubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Http\Resources\Subscription\SubscriptionResource;
use App\Repositories\Subscription\SubscriptionLogicRepository;
use Illuminate\Http\JsonResponse;

class SubscriptionController 
{
    public function __construct(
        private readonly SubscriptionLogicRepository $logic
    ) {}

    public function index()
    {
        return SubscriptionResource::collection(
            $this->logic->getAll()
        );
    }

    public function show(int $id): SubscriptionResource
    {
        return new SubscriptionResource(
            $this->logic->getById($id)
        );
    }

    public function store(CreateSubscriptionRequest $request): SubscriptionResource
    {
        $subscription = $this->logic->create($request->validated());

        return new SubscriptionResource($subscription);
    }

    public function update(UpdateSubscriptionRequest $request, int $id): SubscriptionResource
    {
        $subscription = $this->logic->update($id, $request->validated());

        return new SubscriptionResource($subscription);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->logic->delete($id);

        return response()->json([], 204);
    }
}