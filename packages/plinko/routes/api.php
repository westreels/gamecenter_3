<?php

use Packages\Plinko\Http\Controllers\GameController;

// game routes
Route::name('games.plinko.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/plinko/play', [GameController::class, 'play'])->name('play');
    });
