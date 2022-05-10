<?php

namespace Packages\Raffle\Http\Requests;

use App\Rules\BalanceIsSufficient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Packages\Raffle\Rules\TicketsLimit;

class BuyRaffleTicket extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // note that $this->raffle will be available if Raffle model is type hinted in the corresponding Controller method
        return $this->raffle
            && $this->raffle->is_in_progress
            && !$this->raffle->is_end_date_passed;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'quantity' => [
                'required',
                'integer',
                'min:1',
                new TicketsLimit($this->raffle, $this->user())
            ]
        ];
    }

    public function withValidator($validator)
    {
        $user = $this->user();
        $amount = $this->quantity * $this->raffle->ticket_price;

        // check balance
        $validator->after(function ($validator) use ($user, $amount) {
            $rule = new BalanceIsSufficient($user, $amount);

            if (!$rule->passes()) {
                $validator->errors()->add('quantity', $rule->message());
            }
        });
    }
}
