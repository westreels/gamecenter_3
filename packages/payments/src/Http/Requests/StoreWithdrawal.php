<?php

namespace Packages\Payments\Http\Requests;

use App\Rules\BalanceIsSufficient;
use Packages\Payments\Rules\DepositAmountIsSufficient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Packages\Payments\Rules\WithdrawalDoesNotExceedDailyLimit;
use Packages\Payments\Rules\WithdrawalDoesNotExceedMonthlyLimit;
use Packages\Payments\Rules\WithdrawalDoesNotExceedProfit;
use Packages\Payments\Rules\WithdrawalDoesNotExceedWeeklyLimit;
use Packages\Payments\Services\MulticurrencyPaymentService;
use Packages\Payments\Services\PaymentService;

class StoreWithdrawal extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // given withdrawal method exists and is active
        return $this->withdrawalMethod->enabled;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $rules = [
            'amount' => [
                'required',
                'numeric',
                'min:'.config('payments.withdrawal_min'),
                'max:'.config('payments.withdrawal_max'),
            ]
        ];

        $withdrawalMethod = $this->withdrawalMethod;
        $paymentMethod = $withdrawalMethod->paymentMethod;

        if (!empty($withdrawalMethod->parameters)) {
            $rules['parameters'] = 'required|array';

            // add custom fields validation
            foreach ($withdrawalMethod->parameters as $parameter) {
                if ($parameter->validation) {
                    $rules['parameters.' . $parameter->id] = $parameter->validation;
                }
            }
        }

        // if there is linked payment method
        if ($paymentMethod) {
            $paymentService = PaymentService::createFromModel($withdrawalMethod);

            // extra validation for multicurrency gateways (like coinpayments.net)
            if ($paymentService instanceof MulticurrencyPaymentService) {
                $paymentCurrencies = $paymentService->fetchPaymentCurrencies();
                $rules['payment_currency'] = 'required|in:' . $paymentCurrencies->keys()->implode(',');
            }

            // add system fields validation
            foreach ($paymentMethod->input_parameters as $parameter) {
                if ($parameter->validation) {
                    $rules['parameters.' . $parameter->id] = $parameter->validation;
                }
            }
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $user = $this->user();
        $amount = $this->amount;

        // check balance
        $validator->after(function ($validator) use ($user, $amount) {
            $rules = [
                new BalanceIsSufficient($user, $amount),
                new DepositAmountIsSufficient($user),
                new WithdrawalDoesNotExceedProfit($user, $amount),
                new WithdrawalDoesNotExceedDailyLimit($user, $amount),
                new WithdrawalDoesNotExceedWeeklyLimit($user, $amount),
                new WithdrawalDoesNotExceedMonthlyLimit($user, $amount),
            ];

            foreach ($rules as $rule) {
                if (!$rule->passes()) {
                    $validator->errors()->add('amount', $rule->message());
                }
            }
        });
    }
}
