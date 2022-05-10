<?php

namespace Packages\Payments\Listeners;

use App\Services\AccountService;
use Packages\Payments\Events\DepositCompleted;

class CreateDepositAccountTransaction
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

        // create account transaction
        $accountService = new AccountService($deposit->account);
        $accountService->createTransaction($deposit, $deposit->amount);
    }
}
