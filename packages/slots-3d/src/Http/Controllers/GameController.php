<?php

namespace Packages\Slots3D\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\Slots3D\Http\Requests\Play;
use Packages\Slots3D\Services\GameService;

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
