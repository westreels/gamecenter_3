<?php

namespace Packages\SicBo\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\SicBo\Http\Requests\Play;
use Packages\SicBo\Services\GameService;

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
