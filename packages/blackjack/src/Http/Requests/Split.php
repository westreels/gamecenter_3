<?php

namespace Packages\Blackjack\Http\Requests;

use App\Rules\BalanceIsSufficient;

class Split extends Action
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return parent::authorize();
    }

    public function withValidator($validator)
    {
        $user = $this->user();
        $amount = $this->provablyFairGame->game->bet;

        // check balance
        $validator->after(function ($validator) use ($user, $amount) {
            $rule = new BalanceIsSufficient($user, $amount);

            if (!$rule->passes()) {
                $validator->errors()->add('bet', $rule->message());
            }
        });
    }
}
