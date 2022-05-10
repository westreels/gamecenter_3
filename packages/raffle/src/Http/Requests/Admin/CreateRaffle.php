<?php

namespace Packages\Raffle\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateRaffle extends FormRequest
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
        return [
            'title'                 => 'required',
            'ticket_price'          => 'required|integer|min:1',
            'max_tickets_per_user'  => 'required|integer|min:0',
            'total_tickets'         => 'required|integer|min:1',
            'fee'                   => 'required|numeric|min:0',
            'completion_trigger'    => 'required|in:1,2',
            'duration'              => 'nullable|integer',
            'recurring'             => 'required|boolean',
        ];
    }
}
