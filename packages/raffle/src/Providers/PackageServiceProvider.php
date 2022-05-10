<?php

namespace Packages\Raffle\Providers;

use App\Providers\PackageServiceProvider as DefaultPackageServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Packages\Raffle\Console\Commands\BuyRaffleTickets;
use Packages\Raffle\Console\Commands\CompleteRaffles;

class PackageServiceProvider extends DefaultPackageServiceProvider
{
    protected $packageId = 'raffle';

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        // register package event service provider
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // register commands
        $this->commands([CompleteRaffles::class, BuyRaffleTickets::class]);

        // scheduling commands
        if ($this->app->runningInConsole()) {
            // schedule commands
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                // buy tickets (bots)
                $frequencyMethod = config('raffle.bots.frequency');
                $schedule->command(BuyRaffleTickets::class)->$frequencyMethod();
                // complete raffles
                $schedule->command(CompleteRaffles::class)->everyMinute();
            });
        }
    }
}
