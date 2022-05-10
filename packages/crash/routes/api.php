<?php

use Illuminate\Support\Facades\Route;
use Packages\Crash\Http\Controllers\GameController;

// game routes
Route::name('games.crash.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/crash/{multiplayerGame}/bet', [GameController::class, 'bet'])->name('bet');
        Route::post('api/games/crash/{multiplayerGame}/cash-out', [GameController::class, 'cashOut'])->name('cash-out');
    });
