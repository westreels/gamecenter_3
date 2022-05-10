<?php

namespace App\Http\Requests;

use App\Rules\BalanceIsSufficient;
use App\Services\ProvablyFairGameService;
use Illuminate\Foundation\Http\FormRequest;

class PlayGame extends FormRequest
{
    protected $gamePackageId;
    protected $gameableClass;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->hash) {
            $provablyFairGame = ProvablyFairGameService::get($this->hash, $this->gameableClass);
            return $provablyFairGame && !$provablyFairGame->game;
        }

        return FALSE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'hash' => 'required|size:64',
            'bet' => [
                'required',
                'integer',
                'min:' . config($this->gamePackageId . '.min_bet'),
                'max:' . config($this->gamePackageId . '.max_bet'),
            ]
        ];
    }

    public function withValidator($validator)
    {
        $user = $this->user();
        $amount = $this->bet;

        // check balance
        $validator->after(function ($validator) use ($user, $amount) {
            $rule = new BalanceIsSufficient($user, $amount);

            if (!$rule->passes()) {
                $validator->errors()->add('bet', $rule->message());
            }
        });
    }
}
