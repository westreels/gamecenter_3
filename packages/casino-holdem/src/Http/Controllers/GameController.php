<?php

namespace Packages\CasinoHoldem\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\CasinoHoldem\Http\Requests\Play;
use Packages\CasinoHoldem\Http\Requests\Fold;
use Packages\CasinoHoldem\Http\Requests\Call;
use Packages\CasinoHoldem\Services\GameService;

class GameController extends Controller
{
    public function play(Play $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->deal($request->only(['bet', 'bonus_bet']))
            ->getGame();
    }

    public function fold(Fold $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->fold()
            ->getGame();
    }

    public function call(Call $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->call()
            ->getGame();
    }
}
