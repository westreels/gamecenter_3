<?php

namespace Packages\LuckyWheel\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\LuckyWheel\Http\Requests\Play;
use Packages\LuckyWheel\Services\GameService;

class GameController extends Controller
{
    public function play(Play $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->play($request->only(['bet', 'variation']))
            ->getGame();
    }
}
