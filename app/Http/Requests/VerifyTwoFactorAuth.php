<?php

namespace App\Http\Requests;

use App\Rules\OneTimePasswordIsCorrect;
use Illuminate\Foundation\Http\FormRequest;

class VerifyTwoFactorAuth extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !$this->user()->two_factor_auth_passed;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'one_time_password' => [
                'required',
                new OneTimePasswordIsCorrect($this->user()->totp_secret)
            ],
        ];
    }
}
