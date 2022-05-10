<?php

namespace Packages\Payments\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\Payments\Events\WithdrawalCreated;
use Packages\Payments\Helpers\Queries\WithdrawalQuery;
use Packages\Payments\Http\Requests\StoreWithdrawal;
use Packages\Payments\Models\Withdrawal;
use Packages\Payments\Models\WithdrawalMethod;
use Illuminate\Http\Request;
use Packages\Payments\Services\PaymentService;

class WithdrawalController extends Controller
{
    public function index(Request $request, WithdrawalQuery $query)
    {
        $account = $request->user()->account;

        $items = $query->addWhere(['account_id', '=', $account->id])->get();

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    /**
     * Handle withdrawals form submission
     *
     * @param StoreWithdrawal $request
     */
    public function store(StoreWithdrawal $request, WithdrawalMethod $withdrawalMethod)
    {
        $withdrawal = new Withdrawal();
        $withdrawal->account()->associate($request->user()->account);
        $withdrawal->method()->associate($withdrawalMethod);
        $withdrawal->status = Withdrawal::STATUS_CREATED;
        $withdrawal->amount = $request->amount;
        // when payment_currency is passed the final payment amount is not known, so need to set it to NULL
        $withdrawal->payment_amount =
            $request->payment_currency && $request->payment_currency != $withdrawalMethod->currency
                ? NULL
                : round( $request->amount / $withdrawalMethod->rate, !is_null($withdrawalMethod->decimal_places) ? $withdrawalMethod->decimal_places : 8);
        $withdrawal->payment_currency = $request->payment_currency ?: $withdrawalMethod->currency;

        if ($withdrawalMethod->paymentMethod) {
            $paymentService = PaymentService::createFromModel($withdrawalMethod);
            $withdrawal->parameters = $paymentService->getWithdrawalParameters($request->all());
        } else {
            $withdrawal->parameters = $request->parameters;
        }

        $withdrawal->save();

        event(new WithdrawalCreated($withdrawal));

        return $this->successResponse(__('Withdrawal request successfully submitted.'));
    }
}
