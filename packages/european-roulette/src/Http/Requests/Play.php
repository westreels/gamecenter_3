<?php

namespace Packages\EuropeanRoulette\Http\Requests;

use App\Http\Requests\PlayGame;
use App\Rules\BalanceIsSufficient;
use Packages\EuropeanRoulette\Models\EuropeanRoulette;
use Packages\EuropeanRoulette\Rules\BetsAreValid;

class Play extends PlayGame
{
    protected $gamePackageId = 'european-roulette';
    protected $gameableClass = EuropeanRoulette::class;

    public function rules()
    {
        return [
            'hash' => 'required|size:64',
            'bets' => [
                'required',
                'array',
                new BetsAreValid(),
            ]
        ];
    }

    public function withValidator($validator)
    {
        $user = $this->user();
        $amount = is_array($this->bets) ? array_sum($this->bets) : 0;

        // check balance
        $validator->after(function ($validator) use ($user, $amount) {
            $rule = new BalanceIsSufficient($user, $amount);

            if (!$rule->passes()) {
                $validator->errors()->add('bet', $rule->message());
            }
        });
    }
}
