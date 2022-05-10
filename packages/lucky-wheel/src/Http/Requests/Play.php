<?php

namespace Packages\LuckyWheel\Http\Requests;

use App\Http\Requests\PlayGame;
use Packages\LuckyWheel\Models\LuckyWheel;

class Play extends PlayGame
{
    protected $gamePackageId = 'lucky-wheel';
    protected $gameableClass = LuckyWheel::class;

    public function authorize()
    {
        $config = is_integer($this->variation) ? (config('lucky-wheel.variations')[$this->variation] ?? NULL) : NULL;

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
                    'min:' . config('lucky-wheel.variations')[$this->variation]->min_bet,
                    'max:' . config('lucky-wheel.variations')[$this->variation]->max_bet,
                ]
            ]
        );
    }
}
