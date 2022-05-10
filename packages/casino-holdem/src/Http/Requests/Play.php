<?php

namespace Packages\CasinoHoldem\Http\Requests;

use App\Http\Requests\PlayGame;
use App\Rules\BalanceIsSufficient;
use Packages\CasinoHoldem\Models\CasinoHoldem;

class Play extends PlayGame
{
    protected $gamePackageId = 'casino-holdem';
    protected $gameableClass = CasinoHoldem::class;

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'bonus_bet' => [
                    'required',
                    'integer',
                    'min:' . config($this->gamePackageId . '.min_bonus_bet'),
                    'max:' . config($this->gamePackageId . '.max_bonus_bet'),
                ]
            ]
        );
    }

    public function withValidator($validator)
    {
        $user = $this->user();
        $amount = $this->bet + $this->bonus_bet;

        // check balance
        $validator->after(function ($validator) use ($user, $amount) {
            $rule = new BalanceIsSufficient($user, $amount);

            if (!$rule->passes()) {
                $validator->errors()->add('bet', $rule->message());
            }
        });
    }
}
