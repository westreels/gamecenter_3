<?php

namespace Packages\HorseRacing\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\HorseRacing\Http\Requests\Play;
use Packages\HorseRacing\Services\GameService;

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
