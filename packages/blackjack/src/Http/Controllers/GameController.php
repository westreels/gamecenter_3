<?php

namespace Packages\Blackjack\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\Blackjack\Http\Requests\Play;
use Packages\Blackjack\Http\Requests\HitStand;
use Packages\Blackjack\Http\Requests\Double;
use Packages\Blackjack\Http\Requests\Insurance;
use Packages\Blackjack\Http\Requests\Split;
use Packages\Blackjack\Http\Requests\SplitHitStand;
use Packages\Blackjack\Services\GameService;

class GameController extends Controller
{
    public function play(Play $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->deal($request->only(['bet']))
            ->getGame();
    }

    public function stand(HitStand $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->stand()
            ->getGame();
    }

    public function hit(HitStand $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->hit()
            ->getGame();
    }

    public function double(Double $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->double()
            ->getGame();
    }

    public function insurance(Insurance $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->insurance($request->insurance)
            ->getGame();
    }

    public function split(Split $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->split()
            ->getGame();
    }

    public function splitStand(SplitHitStand $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->splitStand($request->hand)
            ->getGame();
    }

    public function splitHit(SplitHitStand $request, GameService $gameService)
    {
        return $gameService
            ->loadProvablyFairGame($request->hash)
            ->splitHit($request->hand)
            ->getGame();
    }
}
