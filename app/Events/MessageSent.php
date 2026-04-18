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

    public function __construct(ChatMessage $message, $projectId)
    {
        $this->message = $message;
        $this->projectId = $projectId;
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