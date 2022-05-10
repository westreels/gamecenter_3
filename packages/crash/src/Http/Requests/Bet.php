<?php

namespace Packages\Crash\Http\Requests;

use App\Http\Requests\BetMultiplayerGame;
use Illuminate\Auth\Access\AuthorizationException;
use Packages\Crash\Models\Crash;

class Bet extends BetMultiplayerGame
{
    protected $gameableClass = Crash::class;

    public function withValidator($validator)
    {
        $this->validateBalance($validator, $this->user(), $this->bet);
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException(__('The bet can not be accepted at this time.'));
    }
}
