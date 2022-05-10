<?php

namespace Packages\CryptoPrediction\Providers;

use App\Console\Commands\CompleteAssetPredictions;
use App\Providers\PackageServiceProvider as DefaultPackageServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Packages\CryptoPrediction\Models\CryptoPrediction;
use Packages\CryptoPrediction\Services\AssetPredictionService;

class PackageServiceProvider extends DefaultPackageServiceProvider
{
    protected $packageId = 'crypto-prediction';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // register commands and schedules
        if ($this->app->runningInConsole()) {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $schedule->command(CompleteAssetPredictions::class, [
                    '--model' => CryptoPrediction::class,
                    '--service' => AssetPredictionService::class,
                ])->everyThreeMinutes();
            });
        }
    }
}
