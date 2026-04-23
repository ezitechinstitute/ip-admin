<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $projectId;

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id'      => $this->message->id,
                'message' => $this->message->message,
                'sender'  => [
                    'name' => $this->message->sender->name ?? 'User',
                    // DO NOT add the image/base64 field here
                ],
            ],
            'projectId' => $this->projectId,
        ];
    }

    public function __construct(ChatMessage $message, $projectId)
    {
        $this->message = $message;
        $this->projectId = $projectId;
        // Tip: Eager load sender here so the name pops up in real-time
        $this->message->load('sender');
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->projectId);
    }
    public function broadcastAs()
    {
        return 'MessageSent';
    }
}