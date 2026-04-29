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
}
