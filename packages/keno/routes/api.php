<?php

use Packages\Keno\Http\Controllers\GameController;

// game routes
Route::name('games.keno.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/keno/play', [GameController::class, 'play'])->name('play');
    });
