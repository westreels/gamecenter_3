<?php

namespace Packages\Slots\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\Slots\Http\Requests\Play;
use Packages\Slots\Services\GameService;

class GameController extends Controller
{
    public function play(Play $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->play($request->only(['bet', 'lines', 'variation']))
            ->getGame();
    }
}
