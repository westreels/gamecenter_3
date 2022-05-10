<?php

use Packages\MultiplayerBlackjack\Http\Controllers\GameController;

// game routes
Route::name('games.multiplayer-blackjack.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'room.lock', 'last_seen'])
    ->group(function () {
        // game actions
        Route::post('api/games/multiplayer-blackjack/rooms/{room}/play', [GameController::class, 'play'])->name('play');
        Route::post('api/games/multiplayer-blackjack/rooms/{room}/hit', [GameController::class, 'hit'])->name('hit');
        Route::post('api/games/multiplayer-blackjack/rooms/{room}/stand', [GameController::class, 'stand'])->name('stand');
        Route::post('api/games/multiplayer-blackjack/rooms/{room}/complete', [GameController::class, 'complete'])->name('complete');
        Route::post('api/games/multiplayer-blackjack/rooms/{room}/cancel', [GameController::class, 'cancel'])->name('cancel');
    });
