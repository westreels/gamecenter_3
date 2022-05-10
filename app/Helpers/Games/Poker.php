<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace App\Helpers\Games;

use Exception;
use Illuminate\Support\Collection;

class Poker
{
    protected $deck;
    protected $players;
    protected $communityCards;
    protected $pokerPlayerClass = PokerPlayer::class;

    public function __construct(CardDeck $deck = NULL)
    {
        $this->deck = $deck ?: new CardDeck();
        $this->players = collect();
        $this->communityCards = new CardSet();

        return $this;
    }

    /**
     * Add specific number of players to the game
     *
     * @param int $numberOfPlayers
     * @return Poker
     */
    public function addPlayers(int $numberOfPlayers): Poker
    {
        if ($this->getPocketCardsCount() > 0) {
            throw new Exception('You can not add more players after cards are dealt.');
        }

        // init players
        $this->players = $this->players->merge(Collection::times($numberOfPlayers, function ($i) {
            return new $this->pokerPlayerClass;
        }));

        return $this;
    }

    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function getPlayer(int $n): PokerPlayer
    {
        return $this->players->get($n - 1);
    }

    public function getDeck(): CardDeck
    {
        return $this->deck;
    }

    public function getCommunityCards(): CardSet
    {
        return $this->communityCards;
    }

    public function getFlop(): CardSet
    {
        return $this->communityCards->take(3);
    }

    public function deal(int $pocketCardsCount = 2, int $communityCardsCount = 3): Poker
    {
        if ($this->players->count() == 0) {
            throw new Exception('Add players before dealing cards.');
        }

        $this->dealPocketCards($pocketCardsCount);
        $this->dealCommunityCards($communityCardsCount);

        return $this;
    }

    public function dealToPlayer(int $playerNumber, int $numberOfCards = 2): Poker
    {
        if ($numberOfCards == 1) {
            $this->getPlayer($playerNumber)->addPocketCard($this->deck->deal());
        } else {
            $this->getPlayer($playerNumber)->addPocketCards($this->deck->dealSet($numberOfCards));
        }

        return $this;
    }

    protected function dealPocketCards(int $numberOfCards = 2): Poker
    {
        for ($i = 0; $i < $numberOfCards; $i++) {
            $this->players->each(function (PokerPlayer $player) {
                $player->addPocketCard($this->deck->deal());
            });
        }

        return $this;
    }

    protected function dealCommunityCards(int $numberOfCards = 3): Poker
    {
        $this->communityCards = $this->communityCards->concat($this->deck->dealSet($numberOfCards));

        return $this;
    }

    public function play(): Poker
    {
        $this->players->each(function (PokerPlayer $player) {
            $player->makeHand($this->communityCards);
        });

        return $this;
    }

    /**
     * calculate total number of pocket cards for all players
     *
     * @return int
     */
    protected function getPocketCardsCount(): int
    {
        return $this->players->sum(function (PokerPlayer $player) {
            return $player->getPocketCards()->count();
        });
    }
}
