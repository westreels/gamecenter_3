<?php

use Illuminate\Support\Facades\Route;
use Packages\Raffle\Http\Controllers\RaffleController;
use Packages\Raffle\Http\Controllers\Admin\RaffleController as AdminRaffleController;

Route::prefix('api/pub')
    ->middleware(['api'])
    ->group(function () {
        Route::get('raffles', [RaffleController::class, 'featured']);
    });

Route::prefix('api')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'concurrent', 'last_seen'])
    ->group(function () {
        Route::post('raffles/{raffle}', [RaffleController::class, 'buy']);
        Route::get('raffles', [RaffleController::class, 'index']);
    });

// ADMIN ROUTES
Route::prefix('api/admin')
    ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa', 'role:' . App\Models\User::ROLE_ADMIN, 'last_seen'])
    ->group(function () {
        Route::post('raffles/{raffle}/complete', [AdminRaffleController::class, 'complete']);
        Route::get('raffles/{raffle}/tickets', [AdminRaffleController::class, 'tickets']);
        Route::resource('raffles', AdminRaffleController::class)->only(['index', 'show', 'store', 'update']);
    });
