<?php

namespace Packages\Baccarat\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\Baccarat\Http\Requests\Play;
use Packages\Baccarat\Services\GameService;

class GameController extends Controller
{
    public function play(Play $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->play($request->only('player_bet', 'banker_bet', 'tie_bet'))
            ->getGame();
    }
}
