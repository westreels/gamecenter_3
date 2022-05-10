<?php

namespace Packages\Payments\Listeners;

use App\Models\AffiliateCommission;
use App\Models\Bonus;
use App\Services\AccountService;
use App\Services\AffiliateService;
use Packages\Payments\Events\DepositCompleted;

class CreateDepositBonus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DepositCompleted  $event
     * @return void
     */
    public function handle(DepositCompleted $event)
    {
        $deposit = $event->deposit;

        $accountService = new AccountService($deposit->account);

        $depositThreshold = config('settings.bonuses.deposit.threshold');

        // create bonus
        if ($deposit->amount >= $depositThreshold) {
            $accountService->createBonus(
                Bonus::TYPE_DEPOSIT,
                $deposit->amount * config('settings.bonuses.deposit.pct') / 100
            );
        }
    }
}
