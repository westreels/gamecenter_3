<?php

namespace Packages\Payments\Listeners;

use App\Models\AffiliateCommission;
use App\Services\AffiliateService;
use Packages\Payments\Events\DepositCompleted;

class CreateDepositAffiliateCommission
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

        $affiliateService = new AffiliateService($deposit->account);

        $affiliateService->createCommissions(
            $deposit,
            AffiliateCommission::TYPE_DEPOSIT
        );
    }
}
