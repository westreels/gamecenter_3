<?php

namespace Packages\MultiplayerBlackjack\Http\Requests;

use App\Http\Requests\UpdateMultiplayerGame;
use Illuminate\Support\Carbon;

class Cancel extends UpdateMultiplayerGame
{
    public function authorize()
    {
        // parent authorize() needs to run first to initialize required properties
        parent::authorize();

        return $this->room->is_open
            && $this->game // current player started the game
            && $this->room->activePlayers->count() < $this->room->parameters->players_count // someone left, so the room is not full
            && $this->game->created_at->lt(Carbon::now()->subSeconds((int) config('multiplayer-blackjack.cancel_threshold'))); // player waited for certain number of seconds
    }
}
