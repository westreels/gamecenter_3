<?php

namespace Packages\HorseRacing\Http\Requests;

use App\Http\Requests\PlayGame;
use App\Rules\BalanceIsSufficient;
use Illuminate\Validation\Rule;
use Packages\HorseRacing\Helpers\RaceBet;
use Packages\HorseRacing\Models\HorseRacing;

class Play extends PlayGame
{
    protected $gamePackageId = 'horse-racing';
    protected $gameableClass = HorseRacing::class;

    public function rules()
    {
        return [
            'hash' => 'required|size:64',
            'bets' => [
                'required',
                'array',
            ],
            'bets.*.type' => [
                'required',
                'integer',
                Rule::in(RaceBet::getBetTypes()),
            ],
            'bets.*.positions' => [
                'required',
                'array',
                'min:1',
                'max:4', // array length should be from 1 to 4
            ],
            'bets.*.positions.*' => [
                'required',
                'integer',
                'min:0',
                'max:' . (count(config('horse-racing.runners')) - 1),
            ],
            'bets.*.bet' => [
                'required',
                'integer',
                'min:' . config('horse-racing.min_bet'),
                'max:' . config('horse-racing.max_bet'),
            ]
        ];
    }

    public function withValidator($validator)
    {
        $user = $this->user();
        $amount = collect($this->bets)->sum('bet');

        // check balance
        $validator->after(function ($validator) use ($user, $amount) {
            $rule = new BalanceIsSufficient($user, $amount);

            if (!$rule->passes(NULL, NULL)) {
                $validator->errors()->add('bets', $rule->message());
            }
        });
    }
}
