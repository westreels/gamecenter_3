<?php

namespace Packages\HeadsOrTails\Http\Requests;

use App\Http\Requests\PlayGame;
use Packages\HeadsOrTails\Models\HeadsOrTails;

class Play extends PlayGame
{
    protected $gamePackageId = 'heads-or-tails';
    protected $gameableClass = HeadsOrTails::class;

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'toss_bet' => 'required|integer|in:0,1', // 0 - heads, 1 - tails
            ]
        );
    }
}
