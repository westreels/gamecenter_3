<?php

namespace Packages\Dice3D\Http\Requests;

use App\Http\Requests\PlayGame;
use Packages\Dice3D\Models\Dice3D;
use Packages\Dice3D\Services\GameService;

class Play extends PlayGame
{
    protected $gamePackageId = 'dice-3d';
    protected $gameableClass = Dice3D::class;

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'direction'  => 'required|in:-1,1',
                'ref_number' => 'required|integer|min:' . GameService::calcMinRefNumber() . '|max:' . GameService::calcMaxRefNumber(),
            ]
        );
    }
}
