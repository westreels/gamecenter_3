<?php

namespace Packages\Raffle\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CompleteRaffle extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->raffle && $this->raffle->is_in_progress;
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
