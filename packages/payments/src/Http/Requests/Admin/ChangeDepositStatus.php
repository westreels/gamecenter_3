<?php

namespace Packages\Payments\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ChangeDepositStatus extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->deposit->is_created && !$this->deposit->gateway;
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
}
