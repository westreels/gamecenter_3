<?php

namespace Packages\Payments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyBlockchainAddress extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return optional($this->blockchainAddress)->confirmed === 0;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'signature' => 'required|min:100|max:150'
        ];
    }
}
