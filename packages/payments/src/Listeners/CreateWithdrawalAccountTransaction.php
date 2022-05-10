<?php

namespace Packages\Payments\Listeners;

use App\Services\AccountService;
use Packages\Payments\Events\WithdrawalCreated;

class CreateWithdrawalAccountTransaction
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
     * @param  WithdrawalCreated  $event
     * @return void
     */
    public function handle(WithdrawalCreated $event)
    {
        $withdrawal = $event->withdrawal;

        // create account transaction
        $accountService = new AccountService($withdrawal->account);
        $accountService->createTransaction($withdrawal, -$withdrawal->amount);
    }
}
