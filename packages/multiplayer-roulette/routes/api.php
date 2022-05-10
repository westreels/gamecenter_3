<?php

use Illuminate\Support\Facades\Route;
use Packages\MultiplayerRoulette\Http\Controllers\GameController;

// game routes
Route::name('games.multiplayer-roulette.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/multiplayer-roulette/{multiplayerGame}/bet', [GameController::class, 'bet'])->name('bet');
        Route::post('api/games/multiplayer-roulette/{multiplayerGame}/complete', [GameController::class, 'complete'])->name('complete')->middleware(['concurrent', 'game.lock']);
    });
