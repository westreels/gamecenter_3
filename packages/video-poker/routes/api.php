<?php

use Illuminate\Support\Facades\Route;
use Packages\VideoPoker\Http\Controllers\GameController;

// game routes
Route::name('games.video-poker.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/video-poker/play', [GameController::class, 'play'])->name('play');
        Route::post('api/games/video-poker/draw', [GameController::class, 'draw'])->name('draw');
    });
