<?php

namespace Packages\AmericanRoulette\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\AmericanRoulette\Http\Requests\Play;
use Packages\AmericanRoulette\Services\GameService;

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
