<?php

namespace Packages\VideoPoker\Helpers;

use App\Helpers\Games\CardValue;
use App\Helpers\Games\PokerHand as ParentPokerHand;

class PokerHand extends ParentPokerHand
{
    /**
     * Check if hand is better than pair of Jacks
     *
     * @return bool
     * @throws Exception
     */
    public function isPairOfJacksOrBetter(): bool
    {
        return $this->getRank() > self::HAND_ONE_PAIR
            || $this->getRank() == self::HAND_ONE_PAIR && $this->get()->first()->value->rank >= (new CardValue('J'))->rank;
    }
}
