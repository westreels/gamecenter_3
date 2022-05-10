<?php

use Packages\Dice\Http\Controllers\GameController;

// game routes
Route::name('games.dice.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/dice/play', [GameController::class, 'play'])->name('play');
    });
