<?php

namespace Packages\MultiplayerBlackjack\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GameRoom;
use Packages\MultiplayerBlackjack\Http\Requests\Cancel;
use Packages\MultiplayerBlackjack\Http\Requests\Complete;
use Packages\MultiplayerBlackjack\Http\Requests\Play;
use Packages\MultiplayerBlackjack\Http\Requests\Hit;
use Packages\MultiplayerBlackjack\Http\Requests\Stand;
use Packages\MultiplayerBlackjack\Services\GameService;

class GameController extends Controller
{
    public function play(Play $request, GameRoom $room, GameService $gameService)
    {
        return $gameService
            ->setRoom($room)
            ->createGame()
            ->action('play')
            ->getGame();
    }

    public function stand(Stand $request, GameRoom $room, GameService $gameService)
    {
        return $gameService
            ->setRoom($room)
            ->loadGame()
            ->action('stand')
            ->getGame();
    }

    public function hit(Hit $request, GameRoom $room, GameService $gameService)
    {
        return $gameService
            ->setRoom($room)
            ->loadGame()
            ->action('hit')
            ->getGame();
    }

    public function complete(Complete $request, GameRoom $room, GameService $gameService)
    {
        return $gameService
            ->setRoom($room)
            ->loadGame()
            ->action('complete')
            ->getGame();
    }

    public function cancel(Cancel $request, GameRoom $room, GameService $gameService)
    {
        return $gameService
            ->setRoom($room)
            ->loadGame()
            ->action('cancel')
            ->getGame();
    }
}
