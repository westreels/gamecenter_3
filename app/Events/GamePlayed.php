<?php

namespace App\Events;

use App\Models\Game;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Http\Traits\CallApiTraits;

class GamePlayed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    use CallApiTraits;
    public $game;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Game $game, User $user)
    {
        $this->game = $game;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('games');
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
        
        $game = $this->game->only('id', 'bet', 'win', 'title', 'created_ago');

        // $client = new Client();

        $url = config('define.balance_dev.domain') . '/balance-game/' . $this->user->social_id;

        $body = $this->callapi('GET', $url, []);
        
        $game['account'] = $body['data']['data'];
        // $game['account']['balance'] = $body['data']['data']['balance'];
        // dd($game);

        $game['account'] = ['user' => $this->user->only('id', 'name', 'avatar')];

        // $game['account'] = ['user' => $this->game->account->user->only('id', 'name', 'avatar_url')];

        return $game;
    }
}
