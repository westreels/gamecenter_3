<?php

namespace Packages\MultiplayerRoulette\Http\Requests;

use App\Http\Requests\CompleteMultiplayerGame;
use Packages\MultiplayerRoulette\Models\MultiplayerRoulette;

class Complete extends CompleteMultiplayerGame
{
    protected $gameableClass = MultiplayerRoulette::class;
}
