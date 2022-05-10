<?php

use Packages\AmericanRoulette\Http\Controllers\GameController;

// game routes
Route::name('games.american-roulette.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/american-roulette/play', [GameController::class, 'play'])->name('play');
    });
