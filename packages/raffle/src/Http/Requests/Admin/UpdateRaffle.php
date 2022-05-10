<?php

namespace Packages\Raffle\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRaffle extends FormRequest
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
        return [
            'title'                 => 'required',
            'ticket_price'          => 'required|integer|min:1',
            'max_tickets_per_user'  => 'required|integer|min:0',
            'total_tickets'         => 'required|integer|min:1',
            'fee'                   => 'required|numeric|min:0',
            'recurring'             => 'required|boolean',
        ];
    }
}
