<?php

namespace Packages\CasinoHoldem\Helpers;

use App\Helpers\Games\Poker as ParentPoker;
use App\Helpers\Games\PokerPlayer as ParentPokerPlayer;

class Poker extends ParentPoker
{
    protected $pokerPlayerClass = PokerPlayer::class;

    public function player(): ParentPokerPlayer
    {
        return $this->getPlayer(1);
    }

    public function dealer(): ParentPokerPlayer
    {
        return $this->getPlayer(2);
    }
}
