<?php

declare(strict_types=1);

namespace App\Repositories\Friend;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Support\Collection;

class FriendDbRepository
{
    public function getFriends(int $userId): Collection
    {
        return Friend::where(Friend::USER_ID, $userId)
            ->where(Friend::STATUS, 'accepted')
            ->with('friend:id,name,profile_photo')
            ->get()
            ->map(fn($f) => $f->friend);
    }

    public function getPendingRequests(int $userId): Collection
    {
        return Friend::where(Friend::FRIEND_ID, $userId)
            ->where(Friend::STATUS, 'pending')
            ->with('user:id,name,profile_photo')
            ->get()
            ->map(fn($f) => $f->user);
    }

    public function findRelation(int $userId, int $friendId): ?Friend
    {
        return Friend::where(Friend::USER_ID, $userId)
            ->where(Friend::FRIEND_ID, $friendId)
            ->first();
    }

    public function findRequest(int $fromId, int $toId): ?Friend
    {
        return Friend::where(Friend::USER_ID, $fromId)
            ->where(Friend::FRIEND_ID, $toId)
            ->first();
    }

    public function create(int $userId, int $friendId, string $status = 'pending'): Friend
    {
        return Friend::create([
            Friend::USER_ID   => $userId,
            Friend::FRIEND_ID => $friendId,
            Friend::STATUS    => $status,
        ]);
    }

    public function accept(int $fromId, int $toId): void
    {
        Friend::where(Friend::USER_ID, $fromId)
            ->where(Friend::FRIEND_ID, $toId)
            ->update([Friend::STATUS => 'accepted']);

        Friend::firstOrCreate(
            [Friend::USER_ID => $toId, Friend::FRIEND_ID => $fromId],
            [Friend::STATUS => 'accepted']
        );
    }

    public function decline(int $fromId, int $toId): void
    {
        Friend::where(Friend::USER_ID, $fromId)
            ->where(Friend::FRIEND_ID, $toId)
            ->delete();
    }

    public function remove(int $userId, int $friendId): void
    {
        Friend::where(Friend::USER_ID, $userId)
            ->where(Friend::FRIEND_ID, $friendId)
            ->delete();

        Friend::where(Friend::USER_ID, $friendId)
            ->where(Friend::FRIEND_ID, $userId)
            ->delete();
    }

    public function searchUsers(int $userId, string $query): Collection
    {
        $friendIds = Friend::where(Friend::USER_ID, $userId)->pluck(Friend::FRIEND_ID);

        return User::where('name', 'like', '%' . $query . '%')
            ->where('id', '!=', $userId)
            ->whereNotIn('id', $friendIds)
            ->select('id', 'name', 'profile_photo')
            ->limit(10)
            ->get();
    }
}