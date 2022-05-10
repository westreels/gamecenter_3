<?php

namespace Packages\MultiplayerRoulette\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MultiplayerGame;
use Packages\MultiplayerRoulette\Http\Requests\Complete;
use Packages\MultiplayerRoulette\Http\Requests\Bet;
use Packages\MultiplayerRoulette\Services\GameService;

class GameController extends Controller
{
    public function bet(Bet $request, MultiplayerGame $multiplayerGame, GameService $gameService)
    {
        return $gameService
            ->load($multiplayerGame)
            ->bet($request->only('type', 'bet'))
            ->getGame();
    }

    public function complete(Complete $request, MultiplayerGame $multiplayerGame, GameService $gameService)
    {
        return $gameService
            ->load($multiplayerGame)
            ->complete()
            ->getGame();
    }
}
