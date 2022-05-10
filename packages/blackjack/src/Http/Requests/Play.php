<?php

namespace Packages\Blackjack\Http\Requests;

use App\Http\Requests\PlayGame;
use Packages\Blackjack\Models\Blackjack;

class Play extends PlayGame
{
    protected $gamePackageId = 'blackjack';
    protected $gameableClass = Blackjack::class;
}
