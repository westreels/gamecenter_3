<?php

namespace Packages\Keno\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\Keno\Http\Requests\Play;
use Packages\Keno\Services\GameService;

class GameController extends Controller
{
    public function play(Play $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->play($request->only(['bet', 'numbers']))
            ->getGame();
    }
}
