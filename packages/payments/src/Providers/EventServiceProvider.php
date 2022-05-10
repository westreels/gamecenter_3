<?php

namespace Packages\Payments\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Packages\Payments\Events\DepositCancelled;
use Packages\Payments\Events\DepositCompleted;
use Packages\Payments\Events\WithdrawalCancelled;
use Packages\Payments\Events\WithdrawalCompleted;
use Packages\Payments\Events\WithdrawalCreated;
use Packages\Payments\Listeners\CreateDepositAccountTransaction;
use Packages\Payments\Listeners\CreateDepositAffiliateCommission;
use Packages\Payments\Listeners\CreateDepositBonus;
use Packages\Payments\Listeners\CreateWithdrawalAccountTransaction;
use Packages\Payments\Listeners\NotificationSubscriber;
use Packages\Payments\Listeners\ReverseWithdrawalAccountTransaction;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        DepositCancelled::class => [
            //
        ],
        DepositCompleted::class => [
            CreateDepositAccountTransaction::class,
            CreateDepositBonus::class,
            CreateDepositAffiliateCommission::class,
        ],
        WithdrawalCreated::class => [
            CreateWithdrawalAccountTransaction::class, // deduct funds from user account
        ],
        WithdrawalCancelled::class => [
            ReverseWithdrawalAccountTransaction::class, // return funds to user account
        ],
        WithdrawalCompleted::class => [
            //
        ]
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        NotificationSubscriber::class
    ];
}
