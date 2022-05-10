<?php

namespace Packages\Payments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDeposit extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data.user_id' => 'required|integer|exists:users,social_id',
            'data.amount' => 'required|numeric',
            'data.time' => 'required|integer',
            'hash' => 'required|max:255',
        ];
    }
}
