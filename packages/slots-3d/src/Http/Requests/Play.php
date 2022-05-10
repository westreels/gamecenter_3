<?php

namespace Packages\Slots3D\Http\Requests;

use App\Http\Requests\PlayGame;
use Packages\Slots3D\Models\Slots3D;

class Play extends PlayGame
{
    protected $gamePackageId = 'slots-3d';
    protected $gameableClass = Slots3D::class;
}
