<?php

namespace Packages\CasinoHoldem\Helpers;

use App\Helpers\Games\CardSet;
use App\Helpers\Games\PokerPlayer as ParentPokerPlayer;

class PokerPlayer extends ParentPokerPlayer
{
    protected $pokerHandClass = PokerHand::class;

    public function addBonusHand(CardSet $communityCards): PokerHand
    {
        return new PokerHand($this->getPocketCards(), $communityCards);
    }
}
