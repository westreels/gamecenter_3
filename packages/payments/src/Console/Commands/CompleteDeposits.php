<?php

namespace Packages\Payments\Console\Commands;

use IEXBase\TronAPI\Support\Base58Check;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Packages\Payments\Events\DepositCompleted;
use Packages\Payments\Helpers\Ethereum;
use Packages\Payments\Models\Deposit;
use Packages\Payments\Services\PaymentService;

class CompleteDeposits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deposit:complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Complete pending deposits';

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
        $this->completeEthereumDeposits();
        $this->completeTronDeposits();
    }

    protected function completeEthereumDeposits()
    {
        $methods = [];
        // check that API key is specified in the settings
        if (config('payments.ethereum.api_key')) {
            $methods = array_merge($methods, ['ethereum', 'erc20']);
        }

        if (config('payments.bsc.api_key')) {
            $methods = array_merge($methods, ['bnb', 'bep20']);
        }

        if (config('payments.polygon.api_key')) {
            $methods = array_merge($methods, ['matic', 'erc20_polygon']);
        }

        if (!empty($methods)) {
            // get pending deposits
            $deposits = Deposit::pending()
                ->whereNotNull('external_id')
                ->with('method', 'method.paymentMethod')
                ->whereHas('method.paymentMethod', function ($query) use ($methods) {
                    $query->whereIn('code', $methods);
                })
                ->get();

            foreach ($deposits as $deposit) {
                try {
                    Log::info(sprintf('Complete deposit #%d, %f %s (%s wei) = %d credits', $deposit->id, $deposit->payment_amount, $deposit->payment_currency, $deposit->payment_amount_wei, $deposit->amount));

                    // instantiate payment service
                    $paymentService = PaymentService::createFromModel($deposit->method);

                    $transaction = $paymentService->fetchTransaction($deposit->external_id);

                    info(json_encode($transaction));

                    // request is successful
                    if ($transaction) {
                        // ERC20 / BEP20 token transaction
                        if (isset($deposit->parameters->contractAddress)) {
                            $input = $transaction->input;

                            if (strlen($input) == 138) {
                                $method = substr($input, 0, 10);
                                $addressTo = substr($input, 10, 64);
                                $value = substr($input, 74, 64);
                                $paddedDepositAddressTo = str_pad(substr($deposit->parameters->addressTo, 2), 64, '0', STR_PAD_LEFT);
                                $paddedDepositPaymentAmount = Ethereum::fromWeiToPaddedHex($deposit->payment_amount_wei);

                                // check transaction parameters
                                if ($method == '0xa9059cbb'
                                    && strtolower($transaction->from) == strtolower($deposit->parameters->addressFrom)
                                    && strtolower($addressTo) == strtolower($paddedDepositAddressTo)
                                    && $value == $paddedDepositPaymentAmount) {
                                    // mark deposit as completed
                                    $deposit->update(['status' => Deposit::STATUS_COMPLETED]);
                                    event(new DepositCompleted($deposit));

                                    Log::info(sprintf('SUCCESS'));
                                } else {
                                    Log::info(sprintf(
                                        'FROM match: %s, TO match: %s, AMOUNT match: %s, %s, %s',
                                        (int)(strtolower($transaction->from) == strtolower($deposit->parameters->addressFrom)),
                                        (int)(strtolower($addressTo) == strtolower($paddedDepositAddressTo)),
                                        (int)($value == $paddedDepositPaymentAmount),
                                        $value,
                                        $paddedDepositPaymentAmount
                                    ));
                                }

                            } else {
                                Log::info(sprintf('TRX ERROR: input length is incorrect %d', strlen($input)));
                            }
                        // regular ETH / BNB transaction
                        } else {
                            $amount = Ethereum::bchexdec($transaction->value);

                            // check transaction parameters
                            if (strtolower($transaction->from) == strtolower($deposit->parameters->addressFrom)
                                && strtolower($transaction->to) == strtolower($deposit->parameters->addressTo)
                                && $amount == $deposit->payment_amount_wei) {
                                // mark deposit as completed
                                $deposit->update(['status' => Deposit::STATUS_COMPLETED]);
                                event(new DepositCompleted($deposit));

                                Log::info(sprintf('SUCCESS'));
                            } else {
                                Log::info(sprintf(
                                    'FROM match: %s, TO match: %s, AMOUNT match: %s, %s, %s',
                                    (int)(strtolower($transaction->from) == strtolower($deposit->parameters->addressFrom)),
                                    (int)(strtolower($transaction->to) == strtolower($deposit->parameters->addressTo)),
                                    (int)($amount == $deposit->payment_amount_wei),
                                    $amount,
                                    $deposit->payment_amount_wei
                                ));
                            }
                        }
                    } else {
                        Log::info(sprintf('FETCH ERROR: %s', $paymentService->getErrorMessage()));
                    }
                } catch (\Exception $e) {
                    Log::error(sprintf('GENERAL ERROR: %s', $e->getMessage()));
                }

                $this->cancelIfNotCompleted($deposit);
            }
        }
    }

    protected function completeTronDeposits()
    {
        // get pending deposits
        $deposits = Deposit::pending()
            ->whereNotNull('external_id')
            ->with('method', 'method.paymentMethod')
            ->whereHas('method.paymentMethod', function ($query) {
                $query->where('code', 'tron')->orWhere('code', 'trc20');
            })
            ->get();

        foreach ($deposits as $deposit) {
            try {
                Log::info(sprintf('Complete deposit #%d, %f %s = %d credits', $deposit->id, $deposit->payment_amount, $deposit->payment_currency, $deposit->amount));

                // instantiate payment service
                $paymentService = PaymentService::createFromModel($deposit->method);

                $transaction = $paymentService->fetchTransaction($deposit->external_id);
                $transactionStatus = data_get($transaction, 'ret.0.contractRet');

                info(json_encode($transaction));

                // request is successful
                if ($transaction && $transactionStatus == 'SUCCESS') {
                    $depositAddressFromHex =  Base58Check::decode($deposit->parameters->addressFrom,0,3);
                    $depositAddressToHex =  Base58Check::decode($deposit->parameters->addressTo,0,3);

                    // TRC20 token transaction
                    if (isset($deposit->parameters->contractAddress)) {
                        $input = '0x' . data_get($transaction, 'raw_data.contract.0.parameter.value.data');

                        if (strlen($input) == 138) {
                            $method = substr($input, 0, 10);
                            $addressTo = substr($input, 10, 64);
                            $value = substr($input, 74, 64);
                            $paddedDepositAddressTo = str_pad(substr($depositAddressToHex, 2), 64, '0', STR_PAD_LEFT);
                            $paddedDepositPaymentAmount = Ethereum::fromWeiToPaddedHex($deposit->payment_amount_sun);

                            // check transaction parameters
                            if ($method == '0xa9059cbb'
                                && strtolower(data_get($transaction, 'raw_data.contract.0.parameter.value.owner_address')) == $depositAddressFromHex
                                && strtolower($addressTo) == strtolower($paddedDepositAddressTo)
                                && $value == $paddedDepositPaymentAmount) {
                                // mark deposit as completed
                                $deposit->update(['status' => Deposit::STATUS_COMPLETED]);
                                event(new DepositCompleted($deposit));

                                Log::info(sprintf('SUCCESS'));
                            } else {
                                Log::info(sprintf(
                                    'FROM match: %s, TO match: %s, AMOUNT match: %s, %s, %s',
                                    (int)(strtolower(data_get($transaction, 'raw_data.contract.0.parameter.value.owner_address')) == $depositAddressFromHex),
                                    (int)(strtolower($addressTo) == strtolower($paddedDepositAddressTo)),
                                    (int)($value == $paddedDepositPaymentAmount),
                                    $value,
                                    $paddedDepositPaymentAmount
                                ));
                            }

                        } else {
                            Log::info(sprintf('TRX ERROR: input length is incorrect %d', strlen($input)));
                        }
                    // regular TRX transaction
                    } else {
                        // check transaction parameters
                        if (strtolower(data_get($transaction, 'raw_data.contract.0.parameter.value.owner_address')) == $depositAddressFromHex
                            && strtolower(data_get($transaction, 'raw_data.contract.0.parameter.value.to_address')) == $depositAddressToHex
                            && strtolower(data_get($transaction, 'raw_data.contract.0.parameter.value.amount')) == strtolower($deposit->payment_amount_sun)) {
                            // mark deposit as completed
                            $deposit->update(['status' => Deposit::STATUS_COMPLETED]);
                            event(new DepositCompleted($deposit));

                            Log::info(sprintf('SUCCESS'));
                        } else {
                            Log::info(sprintf(
                                'FROM match: %s, TO match: %s, AMOUNT match: %s, %s',
                                (int)(strtolower(data_get($transaction, 'raw_data.contract.0.parameter.value.owner_address')) == $depositAddressFromHex),
                                (int)(strtolower(data_get($transaction, 'raw_data.contract.0.parameter.value.to_address')) == $depositAddressToHex),
                                (int)(strtolower(data_get($transaction, 'raw_data.contract.0.parameter.value.amount')) == strtolower($deposit->payment_amount_sun)),
                                $deposit->payment_amount_sun
                            ));
                        }
                    }
                } else {
                    Log::info(sprintf('FETCH ERROR: %s', $paymentService->getErrorMessage()));
                }
            } catch (\Exception $e) {
                Log::error(sprintf('GENERAL ERROR: %s', $e->getMessage()));
            }

            $this->cancelIfNotCompleted($deposit);
        }
    }

    /**
     * Cancel a deposit that is not completed within certain time frame
     *
     * @param  Deposit  $deposit
     */
    protected function cancelIfNotCompleted(Deposit $deposit): void
    {
        if ($deposit->is_pending && $deposit->created_at->lt(Carbon::now()->subDays(3))) {
            $deposit->update(['status' => Deposit::STATUS_CANCELLED]);
            info('Deposit cancelled');
        }
    }
}
