<?php

use Packages\EuropeanRoulette\Http\Controllers\GameController;

// game routes
Route::name('games.european-roulette.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/european-roulette/play', [GameController::class, 'play'])->name('play');
    });
