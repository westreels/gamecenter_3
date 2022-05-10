<?php

use Packages\Dice3D\Http\Controllers\GameController;

// game routes
Route::name('games.dice-3d.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/dice-3d/play', [GameController::class, 'play'])->name('play');
    });
