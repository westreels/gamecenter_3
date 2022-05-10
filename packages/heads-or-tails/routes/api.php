<?php

use Packages\HeadsOrTails\Http\Controllers\Admin\SettingController;
use Packages\HeadsOrTails\Http\Controllers\GameController;

// game routes
Route::name('games.heads-or-tails.')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        // games
        Route::post('api/games/heads-or-tails/play', [GameController::class, 'play'])->name('play');
    });

// ADMIN ROUTES
Route::prefix('api/admin')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'role:' . App\Models\User::ROLE_ADMIN, 'last_seen'])
    ->group(function () {
        Route::post('settings/heads-or-tails/image', [SettingController::class, 'uploadImage']);
    });
