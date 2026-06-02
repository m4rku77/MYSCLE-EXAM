<?php

declare(strict_types=1);

namespace App\Repositories\Friend;

use Illuminate\Support\Collection;

class FriendLogicRepository
{
    public function __construct(
        private readonly FriendDbRepository $db
    ) {}

    public function getFriends(int $userId): Collection
    {
        return $this->db->getFriends($userId);
    }

    public function getPendingRequests(int $userId): Collection
    {
        return $this->db->getPendingRequests($userId);
    }

    public function sendRequest(int $userId, int $friendId): void
    {
        if ($userId === $friendId) {
            throw new \InvalidArgumentException('You cannot add yourself');
        }

        $existing = $this->db->findRelation($userId, $friendId);
        if ($existing) {
            throw new \InvalidArgumentException('Friend request already sent or already friends');
        }

        $this->db->create($userId, $friendId);
    }

    public function accept(int $fromId, int $toId): void
    {
        $request = $this->db->findRequest($fromId, $toId);
        if (!$request) {
            throw new \InvalidArgumentException('Friend request not found');
        }

        $this->db->accept($fromId, $toId);
    }

    public function decline(int $fromId, int $toId): void
    {
        $this->db->decline($fromId, $toId);
    }

    public function remove(int $userId, int $friendId): void
    {
        $this->db->remove($userId, $friendId);
    }

    public function searchUsers(int $userId, string $query): Collection
    {
        return $this->db->searchUsers($userId, $query);
    }
}