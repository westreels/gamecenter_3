<?php

namespace Packages\SicBo\Http\Requests;

use App\Http\Requests\PlayGame;
use App\Rules\BalanceIsSufficient;
use Illuminate\Validation\Rule;
use Packages\SicBo\Helpers\SicBoBet;
use Packages\SicBo\Models\SicBo;
use Packages\SicBo\Rules\BetsAreValid;

class Play extends PlayGame
{
    protected $gamePackageId = 'sic-bo';
    protected $gameableClass = SicBo::class;

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
                'string',
                Rule::in(SicBoBet::getBetTypes()),
            ],
            'bets.*.numbers' => [
                'present',
                'array',
                'max:2', // array length should be from 0 to 2
            ],
            'bets.*.numbers.*' => [
                'required',
                'integer',
                'min:1',
                'max:17', // Dice: 1-6, Total: 4-17
            ],
            'bets.*.amount' => [
                'required',
                'integer',
                'min:' . config('sic-bo.min_bet'),
                'max:' . config('sic-bo.max_bet'),
            ]
        ];
    }

    public function withValidator($validator)
    {
        $user = $this->user();
        $bets = collect($this->bets)->map(function ($bet) { return (object) $bet; });

        // check balance
        $validator->after(function ($validator) use ($user, $bets) {
            $rules = [
                new BetsAreValid($bets),
                new BalanceIsSufficient($user, $bets->sum('amount'))
            ];

            foreach ($rules as $rule) {
                if (!$rule->passes()) {
                    $validator->errors()->add('bets', $rule->message());
                }
            }
        });
    }
}
