<?php

namespace Packages\Payments\Listeners;

use App\Services\AccountService;
use Packages\Payments\Events\WithdrawalCancelled;

class ReverseWithdrawalAccountTransaction
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
     * @param  WithdrawalCancelled  $event
     * @return void
     */
    public function handle(WithdrawalCancelled $event)
    {
        $withdrawal = $event->withdrawal;

        // create account transaction
        $accountService = new AccountService($withdrawal->account);
        $accountService->createTransaction($withdrawal, $withdrawal->amount);
    }
}
