<?php

use App\Helpers\PackageManager;
use App\Services\DotEnvService;
use App\Services\LicenseService;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('validate', function (PackageManager $pm, LicenseService $ls, DotEnvService $env) {
    set_time_limit(1800);
    $vars = collect();

    if (!$ls->register(env(FP_CODE), env(FP_EMAIL))->success) {
        $vars->push(FP_CODE, FP_HASH, FP_EMAIL);
    }

    if (!app()->runningInConsole()) {
        $vars = $vars->concat(collect($pm->getEnabled())
            ->filter(function ($package) {
                return $package->code_required;
            })
            ->map(function ($package, $id) use ($pm, $ls) {
                return !$ls->register($package->code, env(FP_EMAIL), $package->hash)->success ? $pm->getCodeVariable($id) : NULL;
            })
            ->filter());
    }

    if ($vars->isNotEmpty()) {
        $env->save($vars->mapWithKeys(function ($v) { return [$v => NULL]; })->toArray());
    }
});
