<?php

use Packages\HorseRacing\Http\Controllers\GameController;

// game routes
Route::name('games.horse-racing.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/horse-racing/play', [GameController::class, 'play'])->name('play');
    });
