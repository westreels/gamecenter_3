<?php

namespace App\Http\Requests;

use App\Rules\OneTimePasswordIsCorrect;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmTwoFactorAuth extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // TOTP token is NULL (2FA is disabled)
        return !$this->user()->two_factor_auth_enabled;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'secret' => 'required|string|size:32',
            'one_time_password' => [
                'required',
                new OneTimePasswordIsCorrect($this->secret),
            ]
        ];
    }
}
