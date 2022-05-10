<?php

namespace App\Providers;

use App\Events\GamePlayed;
use App\Listeners\AffiliateEventSubscriber;
use App\Listeners\BonusSubscriber;
use App\Listeners\CreateAccount;
use App\Listeners\NotificationSubscriber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SocialiteProviders\Coinbase\CoinbaseExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Yahoo\YahooExtendSocialite;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SocialiteWasCalled::class => [
            YahooExtendSocialite::class,
            CoinbaseExtendSocialite::class
        ],
        Registered::class => [
            CreateAccount::class
        ],
        GamePlayed::class => [
            //
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        AffiliateEventSubscriber::class,
        BonusSubscriber::class,
        NotificationSubscriber::class,
    ];
}
