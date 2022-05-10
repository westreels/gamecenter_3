<?php

use Illuminate\Support\Facades\Route;
use Packages\GameProviders\Http\Controllers\CallbackController;
use Packages\GameProviders\Http\Controllers\GameController;
use Packages\GameProviders\Http\Controllers\GameProvidersController;
use Packages\GameProviders\Http\Middleware\CallbackIsAuthorized;

Route::name('game-providers.')
    ->prefix('api/providers')
    ->group(function () {
        Route::get('', [GameProvidersController::class, 'getProviders'])
            ->middleware(['api'])
            ->name('providers.all');

        Route::get('games', [GameProvidersController::class, 'getAllGames'])
            ->middleware(['api'])
            ->name('games.all');

        Route::get('{provider}/games', [GameProvidersController::class, 'getProviderGames'])
            ->middleware(['api'])
            ->name('games.by-provider');

        Route::get('{provider}/games/{id}', [GameController::class, 'getGameUrl'])
            ->middleware(['api', 'auth:sanctum', 'verified', 'active', '2fa'])
            ->name('games.url');

        Route::match(['get', 'post'], '{provider}/{action}', [CallbackController::class, 'callback'])
            ->where(['action' => 'play|rollback'])
            ->middleware(CallbackIsAuthorized::class);

    });

