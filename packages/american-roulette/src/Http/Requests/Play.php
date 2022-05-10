<?php

namespace Packages\AmericanRoulette\Http\Requests;

use App\Http\Requests\PlayGame;
use App\Rules\BalanceIsSufficient;
use Packages\AmericanRoulette\Models\AmericanRoulette;
use Packages\AmericanRoulette\Rules\BetsAreValid;

class Play extends PlayGame
{
    protected $gamePackageId = 'american-roulette';
    protected $gameableClass = AmericanRoulette::class;

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
