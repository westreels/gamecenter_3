<?php

namespace App\Helpers\Games;

use Exception;
use Illuminate\Contracts\Support\Arrayable;

class CardDeck implements Arrayable
{
    /**
     * @var CardSet
     */
    protected $deck;

    /**
     * @var CardSet
     */
    protected $dealtCards;

    public function __construct($deck = NULL)
    {
        if ($deck) {
            $this->set($deck);
        } else {
            $this->deck = new CardSet();

            CardSuit::all()->each(function ($suit) {
                CardValue::all()->each(function ($value) use ($suit) {
                    $this->deck->push($suit . $value);
                });
            });

            $this->shuffle();
        }

        $this->dealtCards = new CardSet();

        return $this;
    }

    /**
     * @return CardSet
     */
    public function get(): CardSet
    {
        return $this->deck;
    }

    /**
     * Set card deck to a given deck
     *
     * @param mixed $deck
     * @return CardDeck
     */
    public function set($deck): CardDeck
    {
        $this->deck = new CardSet($deck);

        return $this;
    }

    /**
     * Replace some cards in the deck (useful for testing)
     *
     * @param mixed $cards
     * @return CardDeck
     */
    public function replace($cards): CardDeck
    {
        $this->deck = $this->deck->replace(new CardSet($cards));

        return $this;
    }

    /**
     * Shuffle card deck
     *
     * @return CardDeck
     */
    public function shuffle(): CardDeck
    {
        $this->deck = $this->deck->shuffle(random_int(0, 999999));

        return $this;
    }

    /**
     * Cut N cards from the deck and append to the end
     *
     * @param $numberOfCards
     * @return CardDeck
     */
    public function cut(int $numberOfCards): CardDeck
    {
        // cards under cut index
        $cards = $this->deck->splice($numberOfCards);

        $this->deck = $cards->concat($this->deck);

        return $this;
    }

    /**
     * Deal one top card from the deck
     *
     * @return Card
     */
    public function deal(): Card
    {
        $card = $this->deck->shift();
        $this->dealtCards->push($card);

        return $card;
    }

    /**
     * Deal $numberOfCards from the deck
     *
     * @param  int  $numberOfCards
     * @return CardSet
     */
    public function dealSet(int $numberOfCards): CardSet
    {
        if ($numberOfCards < 1) {
            throw new Exception(sprintf('You should pass an integer greater than 1, %d passed.', $numberOfCards));
        }

        $cards = $this->deck->shift($numberOfCards);
        $this->dealtCards->push(...$cards);

        return $cards;
    }

    /**
     * Take out $numberOfCards from the deck (as if they were already dealt)
     *
     * @param  int  $numberOfCards
     * @return $this
     */
    public function remove(int $numberOfCards): CardDeck
    {
        $this->deck->shift($numberOfCards);

        return $this;
    }

    /**
     * Get n-th card from the deck without pulling it
     *
     * @param $n - starts from 1
     * @return Card
     */
    public function getCard(int $n): Card
    {
        return $this->deck->get($n - 1);
    }

    /**
     * Get count cards from the deck starting from n-th card without pulling them
     *
     * @param int $count
     * @param int $n
     * @return CardSet
     */
    public function getCards(int $count, int $n = 1): CardSet
    {
        return $this->deck->slice($n - 1, $count)->values();
    }

    public function toArray(): array
    {
        return $this->deck->toArray();
    }
}
