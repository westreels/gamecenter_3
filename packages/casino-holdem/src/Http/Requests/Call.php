<?php

namespace Packages\CasinoHoldem\Http\Requests;

use App\Rules\BalanceIsSufficient;
use Illuminate\Http\Request;

class Call extends Action
{
    public function withValidator($validator)
    {
        $user = $this->user();
        $amount = $this->provablyFairGame->game->gameable->ante_bet * 2;

        // check balance
        $validator->after(function ($validator) use ($user, $amount) {
            $rule = new BalanceIsSufficient($user, $amount);

            if (!$rule->passes()) {
                $validator->errors()->add('bet', $rule->message());
            }
        });
    }
}
