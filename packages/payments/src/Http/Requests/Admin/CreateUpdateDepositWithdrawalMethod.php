<?php

namespace Packages\Payments\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Packages\Payments\Models\PaymentGatewayMethod;
use Packages\Payments\Rules\DepositWithdrawalMethodCanBeEnabled;

class CreateUpdateDepositWithdrawalMethod extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return TRUE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $paymentMethod = in_array(Route::currentRouteName(), ['admin.deposit-methods.update', 'admin.withdrawal-methods.update'])
            // update ($this->route()->parameterNames equals depositMethod or withdrawalMethod)
            ? $this->{$this->route()->parameterNames[0]}->paymentMethod
            // create
            : ($this->payment_method_id
                ? PaymentGatewayMethod::find($this->payment_method_id)
                : NULL);

        $rules = [
            'name' => 'required|max:100',
            'currency'  => ['required'],
            'rate' => 'numeric|min:0.00000001',
            'decimal_places' => 'nullable|integer|min:0|max:8',
            'enabled' => [
                'required',
                'integer',
                'in:0,1'
            ]
        ];

        if ($paymentMethod) {
            if (!empty($paymentMethod->allowed_currencies)) {
                $rules['currency'][] = 'in:' . implode(',', $paymentMethod->allowed_currencies);
            }

            if ($this->enabled) {
                $rules['enabled'][] = new DepositWithdrawalMethodCanBeEnabled($paymentMethod);
            }

            // add custom fields validation
            if ($paymentMethod) {
                foreach ($paymentMethod->parameters as $parameter) {
                    if ($parameter->validation) {
                        $rules['payment_method_parameters.' . $parameter->id] = $parameter->validation;
                    }
                }
            }
        }

        return $rules;
    }
}
