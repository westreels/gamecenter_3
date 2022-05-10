<?php

namespace Packages\Dice3D\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\Dice3D\Http\Requests\Play;
use Packages\Dice3D\Services\GameService;

class GameController extends Controller
{
    public function play(Play $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->play($request->only(['bet', 'direction', 'ref_number']))
            ->getGame();
    }
}
