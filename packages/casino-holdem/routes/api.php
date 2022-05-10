<?php

use Packages\CasinoHoldem\Http\Controllers\GameController;

// game routes
Route::name('games.casino-holdem.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/casino-holdem/play', [GameController::class, 'play'])->name('play');
        Route::post('api/games/casino-holdem/fold', [GameController::class, 'fold'])->name('fold');
        Route::post('api/games/casino-holdem/call', [GameController::class, 'call'])->name('call');
    });
