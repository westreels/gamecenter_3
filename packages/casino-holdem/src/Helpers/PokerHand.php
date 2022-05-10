<?php

namespace Packages\CasinoHoldem\Helpers;

use App\Helpers\Games\CardValue;
use App\Helpers\Games\PokerHand as ParentPokerHand;

class PokerHand extends ParentPokerHand
{
    /**
     * Check if hand is better than pair of 4's
     *
     * @return bool
     * @throws \Exception
     */
    public function isPairOfFoursOrBetter(): bool
    {
        return $this->getRank() > self::HAND_ONE_PAIR
            || $this->getRank() == self::HAND_ONE_PAIR && $this->get()->first()->value->rank >= (new CardValue('4'))->rank;
    }

    /**
     * Check if hand is better than pair of aces
     *
     * @return bool
     * @throws \Exception
     */
    public function isPairOfAcesOrBetter(): bool
    {
        return $this->getRank() > self::HAND_ONE_PAIR
            || $this->getRank() == self::HAND_ONE_PAIR && $this->get()->first()->value->is_ace;
    }
}
