<?php

namespace Packages\MultiplayerBlackjack\Http\Requests;

use App\Http\Requests\UpdateMultiplayerGame;

class Complete extends UpdateMultiplayerGame
{
    public function authorize()
    {
        // parent authorize() needs to run first to initialize required properties
        // all hands are expired, but game is not yet completed
        return parent::authorize() && time() > $this->game->gameable->hands->max('action_end');
    }
}
