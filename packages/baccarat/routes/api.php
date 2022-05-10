<?php

use Packages\Baccarat\Http\Controllers\GameController;

// game routes
Route::name('games.baccarat.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/baccarat/play', [GameController::class, 'play'])->name('play');
    });
