<?php

use Packages\Slots\Http\Controllers\Admin\SettingController;
use Packages\Slots\Http\Controllers\GameController;

// game routes
Route::name('games.slots.')
    ->prefix('api')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        // games
        Route::post('games/slots/play', [GameController::class, 'play'])->name('play');
    });

// ADMIN ROUTES
Route::prefix('api/admin')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'role:' . App\Models\User::ROLE_ADMIN, 'last_seen'])
    ->group(function () {
        Route::post('settings/slots/variations/{id}', [SettingController::class, 'cloneVariation']);
        Route::delete('settings/slots/variations/{id}', [SettingController::class, 'removeVariation']);
    });
