<?php

use Packages\LuckyWheel\Http\Controllers\GameController;

// game routes
Route::name('games.lucky-wheel.')
    ->prefix('api')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        // games
        Route::post('games/lucky-wheel/play', [GameController::class, 'play'])->name('play');
    });
