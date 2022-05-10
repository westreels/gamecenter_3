<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ChatRoom $room, ChatMessage $message)
    {
        $this->room = $room;
        $this->message = $message->makeHidden($message->getHidden());
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('chat.' . $this->room->id);
    }

    /**
     * Determine if this event should broadcast.
     *
     * @return bool
     */
    public function broadcastWhen()
    {
        return config('broadcasting.connections.pusher.app_id')
            && config('broadcasting.connections.pusher.key')
            && config('broadcasting.connections.pusher.secret');
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return array_merge(
            $this->message->only('message', 'created_at', 'created_ago'),
            ['user' => $this->message->user->only('id', 'name', 'avatar')],
            [
                'recipients' => $this->message->recipients
                    ? $this->message->recipients->map->only('id', 'name', 'avatar')
                    : []
            ]
        );
    }
}
