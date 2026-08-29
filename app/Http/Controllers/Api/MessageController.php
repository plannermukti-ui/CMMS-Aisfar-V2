<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Message::class);

        $user = $request->user();

        $messages = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender:id,username,full_name,photo', 'receiver:id,username,full_name,photo'])
            ->latest()
            ->paginate(50);

        return response()->json([
            'data' => $messages,
        ]);
    }

    public function store(StoreMessageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['sender_id'] = $request->user()->id;

        $message = Message::create($data);
        $message->load(['sender:id,username,full_name,photo', 'receiver:id,username,full_name,photo']);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message,
        ], 201);
    }

    public function show(Message $message): JsonResponse
    {
        Gate::authorize('view', $message);

        return response()->json([
            'data' => $message->load(['sender:id,username,full_name,photo', 'receiver:id,username,full_name,photo']),
        ]);
    }

    public function update(UpdateMessageRequest $request, Message $message): JsonResponse
    {
        $message->update($request->validated());

        return response()->json([
            'message' => 'Message updated successfully',
            'data' => $message,
        ]);
    }

    public function destroy(Message $message): JsonResponse
    {
        Gate::authorize('delete', $message);

        $message->delete();

        return response()->json([
            'message' => 'Message deleted successfully',
        ]);
    }
}
