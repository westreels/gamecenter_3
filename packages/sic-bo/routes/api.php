<?php

use Packages\SicBo\Http\Controllers\GameController;

// game routes
Route::name('games.sic-bo.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/sic-bo/play', [GameController::class, 'play'])->name('play');
    });
