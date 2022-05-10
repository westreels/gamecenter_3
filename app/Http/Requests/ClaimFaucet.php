<?php

namespace App\Http\Requests;

use App\Rules\FaucetIsAllowed;
use Illuminate\Foundation\Http\FormRequest;

class ClaimFaucet extends FormRequest
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
        return [];
    }

    public function withValidator($validator)
    {
        $user = $this->user();

        $validator->after(function ($validator) use ($user) {
            $rule = new FaucetIsAllowed($user);

            if (!$rule->passes()) {
                $validator->errors()->add('amount', $rule->message());
            }
        });
    }
}
