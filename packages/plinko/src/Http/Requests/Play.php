<?php

namespace Packages\Plinko\Http\Requests;

use App\Http\Requests\PlayGame;
use Packages\Plinko\Models\Plinko;

class Play extends PlayGame
{
    protected $gamePackageId = 'plinko';
    protected $gameableClass = Plinko::class;
}
