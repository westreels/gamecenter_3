<?php

namespace Packages\Dice\Http\Requests;

use App\Http\Requests\PlayGame;
use Packages\Dice\Models\Dice;

class Play extends PlayGame
{
    protected $gamePackageId = 'dice';
    protected $gameableClass = Dice::class;

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'direction'  => 'required|in:-1,1',
                'win_chance' => 'required|numeric|min:' . config('dice.min_win_chance') . '|max:' . config('dice.max_win_chance'),
            ]
        );
    }
}
