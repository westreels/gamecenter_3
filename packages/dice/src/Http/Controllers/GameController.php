<?php

namespace Packages\Dice\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\Dice\Http\Requests\Play;
use Packages\Dice\Services\GameService;

class GameController extends Controller
{
    public function play(Play $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->play($request->only(['bet', 'direction', 'win_chance']))
            ->getGame();
    }
}
