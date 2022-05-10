<?php

namespace Packages\Payments\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Packages\Payments\Events\WithdrawalCancelled;
use Packages\Payments\Models\Withdrawal;
use Packages\Payments\Services\PaymentService;

class ProcessWithdrawals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'withdrawal:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send withdrawals to an external API for processing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $maxWithdrawalAmount = (float) config('payments.withdrawal_auto_max');

        $withdrawals = Withdrawal::where('amount', '<=', $maxWithdrawalAmount)
            ->created()
            ->with('method', 'method.paymentMethod')
            ->whereHas('method.paymentMethod', function ($query) {
                $query->where('code', 'coinpayments');
            })
            ->get();

        foreach ($withdrawals as $withdrawal) {
            try {
                Log::info(sprintf('Auto withdrawal ID %d, amount %s credits', $withdrawal->id, $withdrawal->amount));

                // instantiate payment service
                $paymentService = PaymentService::createFromModel($withdrawal->method);

                // initialize payment
                $paymentService->createWithdrawal([
                    'amount'            => $withdrawal->amount,
                    'payment_currency'  => $withdrawal->payment_currency,
                    'address'           => $withdrawal->parameters->address,
                    'user'              => (object)$withdrawal->account->user->toArray()
                ]);

                // if request is not successful cancel it
                if (!$paymentService->isResponseSuccessful()) {
                    Log::error($paymentService->getErrorMessage());

                    $withdrawal->response = [['error' => $paymentService->getErrorMessage()]];
                    $withdrawal->status = Withdrawal::STATUS_CANCELLED;
                    $withdrawal->save(); // it's important to save the model before raising an event, because they may trigger an exception

                    event(new WithdrawalCancelled($withdrawal));
                // if everything is correct set withdrawal status to pending and save response in the database
                } else {
                    $withdrawal->payment_amount = $paymentService->getPaymentAmount();
                    $withdrawal->external_id = $paymentService->getTransactionReference();
                    $withdrawal->response = [$paymentService->getResponseData()];
                    $withdrawal->status = Withdrawal::STATUS_PENDING;
                    $withdrawal->save();
                }
            } catch (\Exception $e) {
                Log::error(sprintf('ERROR (withdrawal ID %d): %s', $withdrawal->id, $e->getMessage()));
            }
        }
    }
}
