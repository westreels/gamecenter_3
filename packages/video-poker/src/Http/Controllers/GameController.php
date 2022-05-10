<?php

namespace Packages\VideoPoker\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\VideoPoker\Http\Requests\Draw;
use Packages\VideoPoker\Http\Requests\Play;
use Packages\VideoPoker\Services\GameService;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function play(Play $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->play($request->only(['bet']))
            ->getGame();
    }

    public function draw(Draw $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->draw($request->only(['hold']))
            ->getGame();
    }
}
