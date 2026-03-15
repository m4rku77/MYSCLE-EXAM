<?php

declare(strict_types=1);

namespace App\Repositories\Subscription;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionDbRepository
{
    public function __construct(
        private readonly Subscription $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->get();
    }

    public function getById(int $id): Subscription
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Subscription
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Subscription
    {
        $subscription = $this->getById($id);

        $subscription->update($data);

        return $subscription->fresh();
    }

    public function delete(int $id): void
    {
        $subscription = $this->getById($id);

        $subscription->delete();
    }
}