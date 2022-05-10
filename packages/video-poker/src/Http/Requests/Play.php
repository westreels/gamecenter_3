<?php

namespace Packages\VideoPoker\Http\Requests;

use App\Http\Requests\PlayGame;
use Packages\VideoPoker\Models\VideoPoker;

class Play extends PlayGame
{
    protected $gamePackageId = 'video-poker';
    protected $gameableClass = VideoPoker::class;
}
