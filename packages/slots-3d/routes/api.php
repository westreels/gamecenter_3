<?php

use Illuminate\Support\Facades\Route;
use Packages\Slots3D\Http\Controllers\GameController;

// game routes
Route::name('games.slots-3d.')
    ->prefix('api')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        Route::post('games/slots-3d/play', [GameController::class, 'play'])->name('play');
    });

