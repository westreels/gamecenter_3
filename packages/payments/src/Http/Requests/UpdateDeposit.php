<?php

namespace Packages\Payments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeposit extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $paymentMethod = $this->depositMethod->paymentMethod;

        // given deposit method exists and is active
        return $this->depositMethod->enabled
            && $this->depositMethod->id == $this->deposit->deposit_method_id
            && $this->deposit->is_created
            && $paymentMethod
            && in_array($paymentMethod->gateway->code, ['ethereum', 'bsc', 'polygon', 'tron']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'transaction_id' => 'required|unique:deposits,external_id'
        ];

        return $rules;
    }
}
