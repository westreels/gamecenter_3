<?php

use Illuminate\Support\Facades\Route;
use Packages\Installer\Http\Controllers\InstallerController;

// Installation routes
Route::prefix('install')
    ->name('install.')
    ->middleware('web', Packages\Installer\Http\Middleware\RedirectIfInstalled::class)
    ->group(function () {
        // view form
        Route::get('{step}', [InstallerController::class, 'view'])->where('step', '(1|2|3|4)')->name('view');
        // process form submission
        Route::post('{step}/process', [InstallerController::class, 'process'])->where('step', '(1|2|3)')->name('process');
    });
