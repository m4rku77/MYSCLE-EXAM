<?php

declare(strict_types=1);

namespace App\Repositories\Message;

use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

class MessageLogicRepository
{
    public function __construct(
        private readonly MessageDbRepository $db
    ) {}

    public function getAll(): Collection
    {
        return $this->db->getAll();
    }

    public function getById(int $id): Message
    {
        return $this->db->getById($id);
    }

    public function create(array $data): Message
    {
        return $this->db->create($data);
    }

    public function update(int $id, array $data): Message
    {
        return $this->db->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->db->delete($id);
    }
}