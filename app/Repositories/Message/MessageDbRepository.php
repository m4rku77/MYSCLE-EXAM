<?php

declare(strict_types=1);

namespace App\Repositories\Message;

use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

class MessageDbRepository
{
    public function __construct(
        private readonly Message $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->get();
    }

    public function getById(int $id): Message
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Message
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Message
    {
        $message = $this->getById($id);

        $message->update($data);

        return $message->fresh();
    }

    public function delete(int $id): void
    {
        $message = $this->getById($id);

        $message->delete();
    }
}