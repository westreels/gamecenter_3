<?php

namespace Packages\GameProviders\Providers;

use App\Providers\PackageServiceProvider as DefaultPackageServiceProvider;
use Closure;
use Illuminate\Support\Str;
use Packages\GameProviders\Helpers\Api;
use Packages\GameProviders\Http\Controllers\CallbackController;
use Packages\GameProviders\Http\Controllers\GameController;
use Packages\GameProviders\Http\Middleware\CallbackIsAuthorized;
use Packages\GameProviders\Services\CallbackService;

class PackageServiceProvider extends DefaultPackageServiceProvider
{
    protected $packageId = 'game-providers';

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->when(GameController::class)
            ->needs(Api::class)
            ->give(Closure::fromCallable([$this, 'makeApi']));

        $this->app->when(CallbackIsAuthorized::class)
            ->needs(Api::class)
            ->give(Closure::fromCallable([$this, 'makeApi']));

        $this->app->when(CallbackController::class)
            ->needs(CallbackService::class)
            ->give(Closure::fromCallable([$this, 'makeCallbackService']));
    }

    protected function makeApi(): Api
    {
        return Api::make(request()->provider);
    }

    protected function makeCallbackService(): ?CallbackService
    {
        $class = sprintf('Packages\\GameProviders\\Services\\%sCallbackService', Str::ucfirst(request()->provider));
        return class_exists($class) ? app()->make($class, [request()]) : NULL;
    }
}
