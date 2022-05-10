<?php

namespace App\Helpers\Games;

use Illuminate\Support\Collection;

class CardSet extends Collection
{
    public function __construct($cards = [])
    {
        $cards = $cards instanceof Collection ? $cards : collect($cards);

        parent::__construct($cards->map(function ($card) {
            return $card instanceof Card ? $card : new Card($card);
        }));

        return $this;
    }

    /**
     * Push one or more Card objects to collection
     * If strings are passed they are automatically converted to Card objects
     *
     * @param  mixed  ...$cards
     * @return CardSet
     */
    public function push(...$cards): CardSet
    {
        $cards = array_map(function ($card) {
            return $card instanceof Card ? $card : new Card($card);
        }, $cards);

        return parent::push(...$cards);
    }

    public function contains($key, $operator = NULL, $value = NULL)
    {
        return in_array($key, $this->toArray(), TRUE);
    }

    public function toArray(): array
    {
        return array_map(function (Card $card) {
            return (string) $card;
        }, $this->items);
    }

    public function toCollection(): Collection
    {
        return $this->collect();
    }
}
