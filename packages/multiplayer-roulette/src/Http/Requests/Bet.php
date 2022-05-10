<?php

namespace Packages\MultiplayerRoulette\Http\Requests;

use App\Http\Requests\BetMultiplayerGame;
use Illuminate\Validation\Rule;
use Packages\MultiplayerRoulette\Models\MultiplayerRoulette;

class Bet extends BetMultiplayerGame
{
    protected $gameableClass = MultiplayerRoulette::class;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'type' => [
                    'required',
                    'string',
                    Rule::in(MultiplayerRoulette::getBetTypes()),
                ]
            ]
        );
    }

    public function withValidator($validator)
    {
        $this->validateBalance($validator, $this->user(), $this->bet);
    }
}
