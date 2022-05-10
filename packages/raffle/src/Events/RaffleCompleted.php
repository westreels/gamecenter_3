<?php

namespace Packages\Raffle\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Packages\Raffle\Models\Raffle;

class RaffleCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $raffle;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Raffle $raffle)
    {
        $this->raffle = $raffle;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
