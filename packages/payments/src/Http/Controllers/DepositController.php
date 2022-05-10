<?php

namespace Packages\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\Payments\Events\DepositCompleted;
use Packages\Payments\Helpers\Queries\DepositQuery;
use Packages\Payments\Http\Requests\StoreDeposit;
use Packages\Payments\Http\Requests\UpdateDeposit;
use Packages\Payments\Models\Deposit;
use Packages\Payments\Models\DepositMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Packages\Payments\Services\DepositService;
use Packages\Payments\Services\PaymentService;

class DepositController extends Controller
{
    /**
     * Deposits listing
     *
     * @param Request $request
     * @return array
     */
    public function index(Request $request, DepositQuery $query)
    {
        $account = $request->user()->account;

        $items = $query->addWhere(['account_id', '=', $account->id])->get();

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function show(Request $request, Deposit $deposit)
    {
        abort_if(
            $request->user()->account->id != $deposit->account_id
            || !$deposit->is_created
            || !$deposit->method->paymentMethod
            || !in_array($deposit->method->paymentMethod->gateway->code, ['coinpayments', 'ethereum', 'bsc', 'polygon', 'tron']),
            404
        );

        return $deposit->makeVisible('parameters');
    }

    public function store(StoreDeposit $request, DepositMethod $depositMethod)
    {
        // if deposit method is linked to some payment gateway
        if ($depositMethod->paymentMethod) {
            $request->merge(['user' => (object)$request->user()->toArray()]);

            // instantiate payment service
            $paymentService = PaymentService::createFromModel($depositMethod);

            // initialize payment
            $paymentService->createDeposit($request->all());

            // if request is not successful log it and return an error message
            if (!$paymentService->isResponseSuccessful() && !$paymentService->isResponseRedirect()) {
                Log::error($paymentService->getResponseData());

                return $this->errorResponse($paymentService->getErrorMessage());
            }

            // request was successful => make a new deposit model
            $deposit = DepositService::create([
                'account'           => $request->user()->account,
                'method'            => $depositMethod,
                'external_id'       => $paymentService->getTransactionReference() ?: NULL,
                'amount'            => $request->amount,
                'payment_amount'    => $paymentService->getPaymentAmount(),
                'payment_currency'  => $paymentService->getPaymentCurrency(),
                'parameters'        => $paymentService->getDepositParameters(array_merge($request->all(), (array) $paymentService->getResponseData())),
                'response'          => $paymentService->getResponseData() ? [$paymentService->getResponseData()] : NULL,
                'status'            => $paymentService->isResponseSuccessful() ? Deposit::STATUS_COMPLETED : Deposit::STATUS_CREATED,
            ]);

            // payment is completed
            if ($paymentService->isResponseSuccessful()) {
                event(new DepositCompleted($deposit));

                return $this->successResponse();
            // payment requires further action
            } elseif ($paymentService->isResponseRedirect()) {
                return $this->successResponse([
                    'redirect' => [
                        'url'       => $paymentService->getRedirectUrl(),
                        'params'    => ['id' => $deposit->id],
                        'external'  => $paymentService->isExternalRedirect()
                    ]
                ]);
            }
        // manual deposit processing
        } else {
            DepositService::create([
                'account'           => $request->user()->account,
                'method'            => $depositMethod,
                'amount'            => $request->amount,
                'payment_amount'    => round($request->amount / $depositMethod->rate, !is_null($depositMethod->decimal_places) ? $depositMethod->decimal_places : 8),
                'payment_currency'  => $depositMethod->currency,
                'parameters'        => $request->parameters,
            ]);

            return $this->successResponse(__('Deposit is successfully created.'));
        }
    }

    public function update(UpdateDeposit $request, DepositMethod $depositMethod, Deposit $deposit)
    {
        $deposit->update([
            'external_id'   => $request->transaction_id,
            'status'        => Deposit::STATUS_PENDING
        ]);

        return $this->successResponse();
    }
}
