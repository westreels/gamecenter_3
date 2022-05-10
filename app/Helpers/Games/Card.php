<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Helpers\Games;

use App\Helpers\MagicGetters;

class Card
{
    use MagicGetters;

    protected $suit;
    protected $value;

    public function __construct(string $code)
    {
        $this->suit = new CardSuit($code[0]);
        $this->value = new CardValue($code[1]);
    }

    protected function getCode(): string
    {
        return $this->getSuit() . $this->getValue();
    }

    protected function getSuit(): CardSuit
    {
        return $this->suit;
    }

    protected function getValue(): CardValue
    {
        return $this->value;
    }

    /**
     * Check if current card has higher value than given $card
     *
     * @param Card $card
     * @return bool
     */
    public function hasHigherValue(Card $card): bool
    {
        return $this->value->rank > $card->value->rank;
    }

    /**
     * Check if current card has higher value than given $card
     *
     * @param Card $card
     * @return bool
     */
    public function hasLowerValue(Card $card): bool
    {
        return $this->value->rank < $card->value->rank;
    }

    public function __toString()
    {
        return $this->getCode();
    }
}
