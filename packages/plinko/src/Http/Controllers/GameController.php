<?php

namespace Packages\Plinko\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\Plinko\Http\Requests\Play;
use Packages\Plinko\Services\GameService;

class GameController extends Controller
{
    public function play(Play $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->play($request->only(['bet']))
            ->getGame();
    }
}
