<?php

namespace App\Http\Requests;

use App\Rules\BalanceIsSufficient;
use Illuminate\Foundation\Http\FormRequest;

class PlayMultiroomMultiplayerGame extends FormRequest
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
        $player = $this->room->player($this->user());

        return $this->room->is_open // room is open
            && $player // user joined this room
            && !$player->game; // user has not started the game yet
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    public function withValidator($validator)
    {
        $user = $this->user();
        $bet = $this->room->parameters->bet;

        // check balance
        $validator->after(function ($validator) use ($user, $bet) {
            $rule = new BalanceIsSufficient($user, $bet);

            if (!$rule->passes(NULL, NULL)) {
                $validator->errors()->add('balance', $rule->message());
            }
        });
    }
}
