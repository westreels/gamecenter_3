<?php

namespace Packages\Payments\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Packages\Payments\Events\WithdrawalCancelled;
use Packages\Payments\Events\WithdrawalCompleted;
use Packages\Payments\Helpers\Queries\Admin\WithdrawalQuery;
use Packages\Payments\Http\Requests\Admin\ChangeWithdrawalStatus;
use Packages\Payments\Models\Withdrawal;
use Illuminate\Http\Request;
use Packages\Payments\Services\PaymentService;

class WithdrawalController extends Controller
{
    public function index(WithdrawalQuery $query)
    {
        $items = $query
            ->get()
            ->map(function ($withdrawal) {
                $withdrawal->account->user->append(
                    'two_factor_auth_enabled',
                    'two_factor_auth_passed',
                    'is_admin',
                    'is_bot',
                    'is_active'
                );
                return $withdrawal;
            });

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function show(Withdrawal $withdrawal)
    {
        return [
            'withdrawal' => $withdrawal
                ->load('account.user', 'method', 'method.paymentMethod', 'method.paymentMethod.gateway')
                ->makeVisible(['response', 'parameters'])
        ];
    }

    public function transaction(Withdrawal $withdrawal)
    {
        $paymentService = PaymentService::createFromModel($withdrawal->method);

        abort_if(!$withdrawal->external_id, 404);
        abort_if(!method_exists($paymentService, 'fetchWithdrawal'), 404);

        $cacheKey = 'payments.withdrawals.' . $withdrawal->id . '.transaction';

        // get data from cache
        $data = Cache::get($cacheKey);

        // if data is not present in the cache or it's expired
        if (!$data) {
            // request is successful, store data in cache
            if ($data = $paymentService->fetchWithdrawal($withdrawal->external_id)) {
                Cache::put($cacheKey, $data, now()->addMinute());
                // if request is not successful return an error message
            } else {
                return $this->errorResponse($paymentService->getErrorMessage());
            }
        }

        return $this->successResponse(['data' => $data]);
    }

    public function send(ChangeWithdrawalStatus $request, Withdrawal $withdrawal)
    {
        // instantiate payment service
        $paymentService = PaymentService::createFromModel($withdrawal->method);

        // initialize payment
        $paymentService->createWithdrawal([
            'amount'            => $withdrawal->amount,
            'payment_currency'  => $withdrawal->payment_currency,
            'address'           => $withdrawal->parameters->address,
            'user'              => (object) $withdrawal->account->user->toArray()
        ]);

        // if request is not successful log it and return an error message
        if (!$paymentService->isResponseSuccessful()) {
            Log::error($paymentService->getErrorMessage());

            return $this->errorResponse($paymentService->getErrorMessage());
        }

        // if everything is correct set withdrawal status to pending and save response in the database
        $withdrawal->payment_amount = $paymentService->getPaymentAmount();
        $withdrawal->external_id = $paymentService->getTransactionReference();
        $withdrawal->response = [$paymentService->getResponseData()];
        $withdrawal->status = Withdrawal::STATUS_PENDING;
        $withdrawal->save();

        return $this->successResponse(__('Withdrawal transaction with reference :id is created.', ['id' => $paymentService->getTransactionReference()]));
    }

    public function cancel(ChangeWithdrawalStatus $request, Withdrawal $withdrawal)
    {
        $withdrawal->update(['status' => Withdrawal::STATUS_CANCELLED]);

        event(new WithdrawalCancelled($withdrawal));

        return $this->successResponse(__('Withdrawal is rejected.'));
    }

    public function complete(ChangeWithdrawalStatus $request, Withdrawal $withdrawal)
    {
        $withdrawal->update(['status' => Withdrawal::STATUS_COMPLETED]);

        event(new WithdrawalCompleted($withdrawal));

        return $this->successResponse(__('Withdrawal is completed.'));
    }

    public function update(Request $request, Withdrawal $withdrawal)
    {
        $withdrawal->update($request->all());

        return $this->successResponse();
    }
}
