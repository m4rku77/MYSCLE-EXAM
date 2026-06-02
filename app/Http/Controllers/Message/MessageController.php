<?php

declare(strict_types=1);

namespace App\Http\Controllers\Message;

use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Message\UpdateMessageRequest;
use App\Http\Resources\Message\MessageResource;
use App\Repositories\Message\MessageLogicRepository;
use Illuminate\Http\JsonResponse;

class MessageController
{
    public function __construct(
        private readonly MessageLogicRepository $logic
    ) {}

    public function index()
    {
        return MessageResource::collection(
            $this->logic->getAll()
        );
    }

    public function show(int $id): MessageResource
    {
        return new MessageResource(
            $this->logic->getById($id)
        );
    }

    public function store(CreateMessageRequest $request): MessageResource
    {
        $data = $request->validated();

        $data['sender_id'] = auth()->id();

        $message = $this->logic->create($data);

        return new MessageResource($message);
    }

    public function update(UpdateMessageRequest $request, int $id): MessageResource
    {
        $message = $this->logic->update($id, $request->validated());

        return new MessageResource($message);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->logic->delete($id);

        return response()->json([], 204);
    }

    public function getMessages(int $userId)
    {
        return MessageResource::collection(
            $this->logic->getConversation(auth()->id(), $userId)
        );
    }

    public function sendMessage(CreateMessageRequest $request): MessageResource
    {
        $message = $this->logic->send(
            auth()->id(),
            $request->receiver_id,
            $request->text
        );

        return new MessageResource($message);
    }
    // GET /messages/{userId}/last
    public function lastMessage(int $userId)
    {
        $last = \App\Models\Message::where(function ($q) use ($userId) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->where('receiver_id', auth()->id());
        })
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$last) return response()->json(null);

        return response()->json([
            'message' => $last->message,
            'sender_id' => $last->sender_id,
            'read_at' => $last->read_at,
            'created_at' => $last->created_at,
        ]);
    }
}
