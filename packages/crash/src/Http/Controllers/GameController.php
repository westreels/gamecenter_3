<?php

namespace Packages\Crash\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MultiplayerGame;
use Packages\Crash\Http\Requests\CashOut;
use Packages\Crash\Http\Requests\Bet;
use Packages\Crash\Services\GameService;

class GameController extends Controller
{
    public function bet(Bet $request, MultiplayerGame $multiplayerGame, GameService $gameService)
    {
        return $gameService
            ->load($multiplayerGame)
            ->bet($request->only('bet'))
            ->getGame();
    }

    public function cashOut(CashOut $request, MultiplayerGame $multiplayerGame, GameService $gameService)
    {
        return $gameService
            ->load($multiplayerGame)
            ->cashOut()
            ->getGame();
    }
}
