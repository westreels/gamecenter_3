<?php

namespace Packages\MultiplayerBlackjack\Http\Requests;

use App\Http\Requests\PlayMultiroomMultiplayerGame;
use Packages\MultiplayerBlackjack\Models\MultiplayerBlackjack;

class Play extends PlayMultiroomMultiplayerGame
{
    protected $gamePackageId = 'multiplayer-blackjack';
    protected $gameableClass = MultiplayerBlackjack::class;
}
