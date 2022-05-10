<?php

namespace Packages\Payments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Packages\Payments\Helpers\Blockchain;
use Packages\Payments\Rules\BlockchainAddressBalanceIsSufficient;
use Packages\Payments\Rules\BlockchainAddressConfirmed;
use Packages\Payments\Services\MulticurrencyPaymentService;
use Packages\Payments\Services\PaymentService;

class StoreDeposit extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // given gateway exists and is active
        return $this->depositMethod->enabled;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            // note that amount is passed in payment currency, not credits
            'amount' => [
                'required',
                'integer',
                'min:'.config('payments.deposit_min'),
                'max:'.config('payments.deposit_max')
            ]
        ];

        $depositMethod = $this->depositMethod;
        $paymentMethod = $depositMethod->paymentMethod;

        // add custom fields validation
        foreach ($depositMethod->parameters as $parameter) {
            if ($parameter->validation) {
                $rules['parameters.' . $parameter->id] = $parameter->validation;
            }
        }

        // if there is linked payment method
        if ($paymentMethod) {
            $paymentService = PaymentService::createFromModel($depositMethod);

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

            $gateway = $paymentService->getPaymentGateway();
            // extra validation for Stripe
            if ($gateway->code == 'stripe') {
                $rules['parameters.payment_method_id'] = 'required';
            // extra validation for Ethereum
            } elseif (in_array($gateway->code, ['ethereum', 'bsc', 'polygon', 'tron'])) {
                // check that address was confirmed
                $rules['parameters.address'] = [new BlockchainAddressConfirmed($this->user(), Blockchain::getAddressModelClassById($gateway->code))];
                // check address balance
                $rules['amount'][] = new BlockchainAddressBalanceIsSufficient($paymentService, $this->parameters['address']);
            }
        }

        return $rules;
    }
}
