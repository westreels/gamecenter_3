<?php

namespace Packages\EuropeanRoulette\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\EuropeanRoulette\Http\Requests\Play;
use Packages\EuropeanRoulette\Services\GameService;

class GameController extends Controller
{
    public function play(Play $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->play($request->only(['bets']))
            ->getGame();
    }
}
