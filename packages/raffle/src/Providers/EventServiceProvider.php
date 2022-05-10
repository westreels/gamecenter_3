<?php

namespace Packages\Raffle\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Packages\Raffle\Events\RaffleCompleted;
use Packages\Raffle\Listeners\SendNotificationToWinner;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        RaffleCompleted::class => [
            SendNotificationToWinner::class // send a notification to user
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
