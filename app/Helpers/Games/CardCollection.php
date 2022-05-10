<?php

namespace App\Helpers\Games;

use Illuminate\Support\Collection;

class CardCollection
{
    private $cards;
    private $cardsSortedByRankAcesLast;
    private $cardsSortedByRankAcesFirst;

    public function __construct(Collection $cards)
    {
        $this->cards = $cards->map(function ($card) {
            return $card instanceof Card ? $card : new Card($card);
        });
    }

    /**
     * Get cards sorted by their rank from lowest to highest taking suits into account,
     * so "SA" is of higher rank than "DA"
     *
     * @param bool $acesFirst - treat aces as lowest ranking cards
     * @return Collection
     */
    public function getCardsSortedByRank($acesFirst = FALSE): Collection
    {
        return $acesFirst ? $this->getCardsSortedByRankAcesFirst() : $this->getCardsSortedByRankAcesLast();
    }

    private function getCardsSortedByRankAcesFirst(): Collection
    {
        if ($this->cardsSortedByRankAcesFirst) {
            return $this->cardsSortedByRankAcesFirst;
        }

        $this->cardsSortedByRankAcesFirst = $this->cards
            ->sortBy(function ($card) {
                return $card->value->is_ace
                    ? -1 + $card->suit->rank / 10
                    : $card->value->rank + $card->suit->rank / 10;
            })
            ->values();

        return $this->cardsSortedByRankAcesFirst;
    }

    private function getCardsSortedByRankAcesLast(): Collection
    {
        if ($this->cardsSortedByRankAcesLast) {
            return $this->cardsSortedByRankAcesLast;
        }

        $this->cardsSortedByRankAcesLast = $this->cards
            ->sortBy(function ($card) {
                return $card->value->rank + $card->suit->rank / 10;
            })
            ->values();

        return $this->cardsSortedByRankAcesLast;
    }

    /**
     * Get suites with count of cards of each suite, e.g. ["S" => 2, "H" => 3]
     *
     * @return Collection
     */
    public function getSuitesWithCount(): Collection
    {
        return $this->getCardsSortedByRank()
            ->mapToGroups(function($card) {
                return [$card->suit->code => 1];
            })
            ->map
            ->sum()
            ->sort();
    }

    /**
     * Get card values with count of cards of each value, e.g. ["2" => 1, "J" => 2, "Q" => 3]
     *
     * @return Collection
     */
    public function getValuesWithCount(): Collection
    {
        return $this->getCardsSortedByRank()
            ->mapToGroups(function($card) {
                return [$card->value->code => 1];
            })
            ->map
            ->sum()
            ->sort();
    }

    /**
     * Get collection of N cards of the same rank, e.g. pair, three of a kind etc
     *
     * @param int $n
     * @return Collection
     */
    public function getNCardsOfSameRank(int $n)
    {
        $values = $this->getValuesWithCount()
            ->filter(function($valueCount) use ($n) {
                return $valueCount == $n;
            });

        if ($values->isEmpty()) {
            return $values;
        }

        return $values
            ->map(function ($n, $value) {
                return $this->getCardsSortedByRank()
                    ->filter(function ($card) use ($value) {
                        return $card->value->code == $value;
                    })
                    ->values();
            });
    }

    public function getFlushCards(int $max = NULL): Collection
    {
        $suit = $this->getSuitesWithCount()->keys()->last();

        return $this->getCardsSortedByRank()
            ->filter(function ($card) use ($suit) {
                return $card->suit->code == $suit;
            })
            ->take($max ? -$max : NULL)
            ->values();
    }


    /**
     * Get longest sequence of cards with consecutive rank (without taking suits into account), e.g. "S2","H3","D4"
     *
     * @param int|NULL $max - limit number of cards in the returned collection
     * @return Collection
     */
    public function getStraightCards(int $max = NULL): Collection
    {
        $sequences = collect();
        $lastRank = 0;

        // consider situations where aces are lowest and highest ranking cards
        foreach (collect([$this->getCardsSortedByRank(TRUE), $this->getCardsSortedByRank()]) as $cards) {
            foreach ($cards as $i => $card) {
                if ($i > 0 && ($card->value->rank == $lastRank + 1
                        // current card is 2 and the previous card in the current sequence was ace
                        || $card->value->is_two && $sequences->last()->first()->value->is_ace)) {
                    $sequences->last()->push($card);
                } else {
                    $sequences->push(collect([$card]));
                }

                $lastRank = $card->value->rank;
            }
        }

        return $sequences
            ->sortBy(function ($sequence) {
                return $sequence->count();
            })
            ->last()
            ->take($max ? -$max : NULL)
            ->values();
    }

    /**
     * Get the card with the highest rank (aces last)
     *
     * @return Card
     */
    public function getCardWithHighestRank(): Card
    {
        return $this->getCardsSortedByRank()->last();
    }
}
