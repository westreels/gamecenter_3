<?php

namespace Packages\Slots\Http\Requests;

use App\Http\Requests\PlayGame;
use App\Rules\BalanceIsSufficient;
use Packages\Slots\Models\Slots;

class Play extends PlayGame
{
    protected $gamePackageId = 'slots';
    protected $gameableClass = Slots::class;

    public function authorize()
    {
        $config = is_integer($this->variation) ? (config('slots.variations')[$this->variation] ?? NULL) : NULL;

        return parent::authorize() && $config;
    }

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'bet' => [
                    'required',
                    'integer',
                    'min:' . config('slots.variations')[$this->variation]->min_bet,
                    'max:' . config('slots.variations')[$this->variation]->max_bet,
                ],
                'lines'  => 'required|integer|min:1|max:20'
            ]
        );
    }

    public function withValidator($validator)
    {
        $user = $this->user();
        $amount = $this->bet * $this->lines;

        // check balance
        $validator->after(function ($validator) use ($user, $amount) {
            $rule = new BalanceIsSufficient($user, $amount);

            if (!$rule->passes(NULL, NULL)) {
                $validator->errors()->add('bet', $rule->message());
            }
        });
    }
}
