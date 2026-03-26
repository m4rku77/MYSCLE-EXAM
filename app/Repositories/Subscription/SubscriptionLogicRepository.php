<?php

declare(strict_types=1);

namespace App\Repositories\Subscription;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionLogicRepository
{
    public function __construct(
        private readonly SubscriptionDbRepository $db
    ) {}

    public function getAll(): Collection
    {
        return $this->db->getAll();
    }

    public function getById(int $id): Subscription
    {
        return $this->db->getById($id);
    }

    public function create(array $data): Subscription
    {
        return $this->db->create($data);
    }

    public function update(int $id, array $data): Subscription
    {
        return $this->db->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->db->delete($id);
    }
}
