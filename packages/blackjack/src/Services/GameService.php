<?php

namespace Packages\Blackjack\Services;

use App\Helpers\Games\CardDeck;
use App\Models\Game;
use App\Models\User;
use App\Services\GameService as ParentGameService;
use Packages\Blackjack\Models\Blackjack;

class GameService extends ParentGameService
{
    protected $gameableClass = Blackjack::class;
    protected $makeVisible = ['dealer_hand', 'dealer_score', 'dealer_blackjack'];

    private $deck;

    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->deck = new CardDeck();
    }

    public function makeSecret(): string
    {
        return implode(',', $this->deck->toArray());
    }

    /**
     * Deal initial set of cards
     *
     * @param $params
     */
    public function deal($params): GameService
    {
        $provablyFairGame = $this->getProvablyFairGame();

        // load initially shuffled deck
        $this->deck->set(explode(',', $provablyFairGame->secret));
        // cut the deck (provably fair)
        $this->deck->cut($provablyFairGame->shift_value % 52);

        $gameable = new $this->gameableClass();
        $gameable->deck = $this->deck->toArray();
        $gameable->actions = [];
        $gameable->win = 0;
        $gameable->bet = $params['bet']; // bet amount in credits
        $gameable->player_hand = [$this->deck->getCard(1)->code, $this->deck->getCard(3)->code];
        $gameable->dealer_hand = [$this->deck->getCard(2)->code, $this->deck->getCard(4)->code];

//        TESTING
//        -------
//        DEALER BLACKJACK (NO INSURANCE)
//        $gameable->dealer_hand = ['DT','SA'];
//
//        DEALER BLACKJACK (INSURANCE)
//        $gameable->dealer_hand = ['SA','ST'];
//
//        PLAYER BLACKJACK
//        $gameable->player_hand = ['DT','SA'];
//
//        PLAYER BLACKJACK (INSURANCE)
//        $gameable->dealer_hand = ['HA','D3'];
//        $gameable->player_hand = ['DT','SA'];
//
//        DEALER & PLAYER BLACKJACK (NO INSURANCE)
//        $gameable->dealer_hand = ['HK','CA'];
//        $gameable->player_hand = ['DJ','SA'];
//
//        DEALER & PLAYER BLACKJACK (INSURANCE)
//        $gameable->dealer_hand = ['CA','HK'];
//        $gameable->player_hand = ['DJ','SA'];
//
//        INSURANCE (NO BLACKJACK)
//        $gameable->dealer_hand = ['SA','H2'];
//
//        INSURANCE (NO BLACKJACK) + 21 POINTS (3 CARDS)
//        $gameable->dealer_hand = ['SA','H2'];
//        $gameable->player_hand = ['C8','H6'];
//        $gameable->deck = array_merge(array_slice($gameable->deck, 0, 4), ['D7'], array_slice($gameable->deck, 5));
//
//        TIE
//        $gameable->dealer_hand = ['ST','H9'];
//        $gameable->player_hand = ['HT','D9'];
//
//        SPLIT
//        $gameable->player_hand = ['S2','H2'];
//
//        SPLIT (FIRST HAND 21 IMMEDIATELY)
//        $gameable->player_hand = ['ST','HT'];
//        $gameable->deck = array_merge(array_slice($gameable->deck, 0, 4), ['CA'], array_slice($gameable->deck, 5));
//
//        SPLIT (FIRST HAND 21 POINTS AFTER HIT)
//        $gameable->player_hand = ['C8','H8'];
//        $gameable->deck = array_merge(array_slice($gameable->deck, 0, 4), ['DT','S3'], array_slice($gameable->deck, 6));
//
//        SPLIT (SECOND HAND 21 POINTS AFTER STAND)
//        $gameable->player_hand = ['CT','HT'];
//        $gameable->deck = array_merge(array_slice($gameable->deck, 0, 4), ['S9','DA'], array_slice($gameable->deck, 6));
//
//        SPLIT (BOTH HANDS 21 IMMEDIATELY)
//        $gameable->player_hand = ['ST','HT'];
//        $gameable->deck = array_merge(array_slice($gameable->deck, 0, 4), ['CA'], ['DA'], array_slice($gameable->deck, 6));
//
//        21 POINTS (3 CARDS)
//        $gameable->player_hand = ['C8','H6'];
//        $gameable->deck = array_merge(array_slice($gameable->deck, 0, 4), ['D7'], array_slice($gameable->deck, 5));
//
//        ---------------------------------------------------------------------

        $gameable->dealer_score = $this->calculateScore($gameable->dealer_hand);
        $gameable->player_score = $this->calculateScore($gameable->player_hand);

        // important to save a reference to gameable in the class property, so it can be used in the parent class
        $this->gameable = $gameable;

        // if the dealer face up card is NOT an ace (which means that insurance is not available even if the dealer has blackjack)
        // or if player doesn't have enough funds to buy insurance
        // note that at this point the user account is not charged yet, so all checks need to consider it
        if ($gameable->dealer_hand[0][1] != 'A' || $this->getUserAccountBalance() < 1.5 * $gameable->bet) {
            // if player has blackjack
            if ($gameable->player_score == 21 && $gameable->dealer_score != 21) {
                $gameable->player_blackjack = TRUE;
                $gameable->win = $gameable->bet * 2.5; // blackjack pays 3/2
                $this->save(['bet' => $gameable->bet, 'win' => $gameable->win, 'status' => Game::STATUS_COMPLETED]);
            // if dealer has blackjack
            } elseif ($gameable->player_score != 21 && $gameable->dealer_score == 21) {
                $gameable->dealer_blackjack = TRUE;
                $this->save(['bet' => $gameable->bet, 'win' => 0, 'status' => Game::STATUS_COMPLETED]);
            // if both player and dealer have blackjack (push)
            } elseif ($gameable->player_score == 21 && $gameable->dealer_score == 21) {
                $gameable->player_blackjack = TRUE;
                $gameable->dealer_blackjack = TRUE;
                $gameable->win = $gameable->bet; // return bet
                $this->save(['bet' => $gameable->bet, 'win' => $gameable->win, 'status' => Game::STATUS_COMPLETED]);
            }

            // game is not finished (there was no blackjack)
            if (!$this->isGameCompleted()) {
                $this->addActions($gameable, ['hit', 'stand']);
                // allow player to double the bet if user has sufficient funds
                if ($this->getUserAccountBalance() >= $gameable->bet * 2) {
                    $this->addActions($gameable, ['double']);
                    if ($gameable->player_hand[0][1] == $gameable->player_hand[1][1]) {
                        $this->addActions($gameable, ['split']);
                    }
                }
                $this->save(['bet' => $gameable->bet, 'win' => 0, 'status' => Game::STATUS_IN_PROGRESS]);
            }
        // dealer face up card is an ace and user has sufficient funds to place insurance bet
        } elseif ($gameable->dealer_hand[0][1] == 'A' && $this->getUserAccountBalance() >= $gameable->bet / 2) {
            // allow player to take insurance
            $this->addActions($gameable, ['insurance']);
            // save game
            $this->save(['bet' => $gameable->bet, 'win' => 0, 'status' => Game::STATUS_IN_PROGRESS]);
        }

        return $this;
    }

    /**
     * Stand (don't hit any cards and wait for the dealer to complete the game)
     *
     * @return GameService
     * @throws \Exception
     */
    public function stand(): GameService
    {
        $gameable = $this->getGameable();

        // load saved deck
        $this->deck->set($gameable->deck);

        $gameable->actions = [];

        while ($this->calculateScore($gameable->dealer_hand) < 17) {
            // Avoid "Indirect modification of overloaded property has no effect" error
            // See more at https://stackoverflow.com/a/34467735/10001962
            $gameable->dealer_hand = array_merge(
                $gameable->dealer_hand,
                [$this->deck->getCard(count($gameable->player_hand) + count($gameable->dealer_hand) + 1)->code]
            );
        }

        $gameable->dealer_score = $this->calculateScore($gameable->dealer_hand);

        // if dealer busted player wins
        if ($gameable->dealer_score > 21 || $gameable->player_score > $gameable->dealer_score) {
            $gameable->win = $gameable->bet * 2; // win pays 1:1
        // score is equal
        } elseif ($gameable->player_score == $gameable->dealer_score) {
            $gameable->win = $gameable->bet; // push, return bet
        // otherwise dealer wins
        } else {
            $gameable->win = 0;
        }

        $this->save([
            'bet' => $gameable->bet + $gameable->insurance_bet,
            'win' => $gameable->win,
            'status' => Game::STATUS_COMPLETED
        ]);

        return $this;
    }

    /**
     * Hit one more card from the deck
     *
     * @return GameService
     */
    public function hit(): GameService
    {
        $gameable = $this->getGameable();

        // load saved deck
        $this->deck->set($gameable->deck);

        $gameable->actions = [];

        // hit one card
        $gameable->player_hand = array_merge(
            $gameable->player_hand,
            [$this->deck->getCard(count($gameable->player_hand) + count($gameable->dealer_hand) + 1)->code]
        );

        $gameable->player_score = $this->calculateScore($gameable->player_hand);

        // player got 21, stand
        if ($gameable->player_score == 21) {
            return $this->stand();
        // player busts
        } else if ($gameable->player_score > 21) {
            $status = Game::STATUS_COMPLETED;
        // player score is under 21
        } else {
            $status = Game::STATUS_IN_PROGRESS;
            $this->addActions($gameable, ['hit', 'stand']);
        }

        $this->save(['status' => $status]);

        return $this;
    }

    /**
     * Double the initial bet, hit one more card from the deck and finish the game
     *
     * @return GameService
     * @throws \Exception
     */
    public function double(): GameService
    {
        $gameable = $this->getGameable();

        // load saved deck
        $this->deck->set($gameable->deck);

        $gameable->actions = [];

        // double the bet
        $gameable->bet *= 2;

        // hit one card
        $gameable->player_hand = array_merge(
            $gameable->player_hand,
            [$this->deck->getCard(count($gameable->player_hand) + count($gameable->dealer_hand) + 1)->code]
        );
        $gameable->player_score = $this->calculateScore($gameable->player_hand);

        // if player didn't bust then stand
        if ($gameable->player_score <= 21) {
            return $this->stand();
        }

        $this->save([
            'bet' => $gameable->bet + $gameable->insurance_bet,
            'win' => $gameable->win,
            'status' => Game::STATUS_COMPLETED
        ]);

        return $this;
    }

    public function insurance($insurance): GameService
    {
        $gameable = $this->getGameable();

        // load saved deck
        $this->deck->set($gameable->deck);

        $gameable->actions = [];

        // if insurance requested
        if ($insurance) {
            // deduct insurance bet from user account
            $gameable->insurance_bet = $gameable->bet / 2;

            // if player has blackjack and dealer does not
            if ($gameable->player_score == 21 && $gameable->dealer_score != 21) {
                $gameable->player_blackjack = TRUE;
                $gameable->insurance_win = 0;
                $gameable->win = $gameable->bet * 2.5; // blackjack pays 3/2
            // if dealer has blackjack and player does not
            } elseif ($gameable->player_score != 21 && $gameable->dealer_score == 21) {
                $gameable->dealer_blackjack = TRUE;
                $gameable->insurance_win = $gameable->insurance_bet * 3; //insurance pays 2:1
                $gameable->win = 0;
            // if both player and dealer have blackjack
            } elseif ($gameable->player_score == 21 && $gameable->dealer_score == 21) {
                $gameable->player_blackjack = TRUE;
                $gameable->dealer_blackjack = TRUE;
                $gameable->insurance_win = $gameable->insurance_bet * 3; //insurance pays 2:1
                $gameable->win = $gameable->bet; // return bet
            } else {
                $this->addActions($gameable, ['hit', 'stand']);
                // allow player to double the bet if user has sufficient funds
                // note that by this time insurance bet was not yet charged, so need to take this into account
                if ($this->getUserAccountBalance() >= $gameable->insurance_bet + $gameable->bet) {
                    $this->addActions($gameable, ['double']);
                    if ($gameable->player_hand[0][1] == $gameable->player_hand[1][1]) {
                        $this->addActions($gameable, ['split']);
                    }
                }
            }
        // insurance is not requested
        } else {
            // if player has blackjack and dealer does not
            if ($gameable->player_score == 21 && $gameable->dealer_score != 21) {
                $gameable->player_blackjack = TRUE;
                $gameable->win = $gameable->bet * 2.5; // blackjack pays 3/2
            // if dealer has blackjack and player does not
            } elseif ($gameable->player_score != 21 && $gameable->dealer_score == 21) {
                $gameable->dealer_blackjack = TRUE;
                $gameable->win = 0;
            // if both player and dealer have blackjack
            } elseif ($gameable->player_score == 21 && $gameable->dealer_score == 21) {
                $gameable->player_blackjack = TRUE;
                $gameable->dealer_blackjack = TRUE;
                $gameable->win = $gameable->bet; // return bet
            } else {
                $this->addActions($gameable, ['hit', 'stand']);
                // allow player to double the bet if user has sufficient funds
                if ($this->getUserAccountBalance() >= $gameable->bet) {
                    $this->addActions($gameable, ['double']);
                    if ($gameable->player_hand[0][1] == $gameable->player_hand[1][1]) {
                        $this->addActions($gameable, ['split']);
                    }
                }
            }
        }

        $this->save(
            [
                'bet' => $gameable->bet + $gameable->insurance_bet, // actual bet
                'win' => $gameable->win + $gameable->insurance_win, // actual win
                'status' => $gameable->dealer_blackjack || $gameable->player_blackjack ? Game::STATUS_COMPLETED : Game::STATUS_IN_PROGRESS
            ]
        );

        return $this;
    }

    public function split(): GameService
    {
        $gameable = $this->getGameable();

        // load saved deck
        $this->deck->set($gameable->deck);

        $playerHand = $gameable->player_hand;

        // split hands
        $gameable->player_hand = [$playerHand[0], $this->deck->getCard(5)->code]; // get 1 new card, for the first split hand
        $gameable->player_score = $this->calculateScore($gameable->player_hand);
        $gameable->player_hand2 = [$playerHand[1]]; // the second hand is not touched until the first is played
        $gameable->player_score2 = $this->calculateScore($gameable->player_hand2);
        $gameable->bet2 = $gameable->bet;

        $win = 0;
        $gameable->actions = ['stand', 'hit'];
        $status = Game::STATUS_IN_PROGRESS;

        // if first hand has 21 points then stand and move to the 2nd hand
        if ($gameable->player_score == 21) {
            $this->splitStand(1, FALSE);
        }

        // if after first hand stand second hand also has 21 points
        if ($gameable->player_score2 == 21) {
            // stand and complete the game
            $this->splitStand(2, FALSE);
            $gameable->actions = [];
            $win = $gameable->win + $gameable->win2;
            $status = Game::STATUS_COMPLETED;
        }

        $this->save([
            'bet' => $gameable->bet * 2,
            'win' => $win,
            'status' => $status
        ]);

        return $this;
    }

    /**
     * Stand (don't hit any cards and wait for the dealer to complete the game)
     *
     * @return GameService
     * @throws \Exception
     */
    public function splitStand(int $hand, bool $save = TRUE): GameService
    {
        $gameable = $this->getGameable();

        // load saved deck
        $this->deck->set($gameable->deck);

        $gameable->actions = [];
        $gameAttributes = [];

        if ($hand == 1) {
            // hit one more card for the second hand
            $gameable->player_hand2 = array_merge($gameable->player_hand2, [$this->deck->getCard(count($gameable->player_hand) + count($gameable->dealer_hand) + 2)->code]);
            $gameable->player_score2 = $this->calculateScore($gameable->player_hand2);
            if ($gameable->player_score2 < 21) {
                $this->addActions($gameable, ['stand', 'hit']);
            }
        }

        // if second hand is played or after stand on the first hand second hand gets 21 points
        if ($hand == 2 || $gameable->player_score2 == 21) {
            while ($this->calculateScore($gameable->dealer_hand) < 17) {
                // Avoid "Indirect modification of overloaded property has no effect" error
                // See more at https://stackoverflow.com/a/34467735/10001962
                $gameable->dealer_hand = array_merge(
                    $gameable->dealer_hand,
                    [$this->deck->getCard(count($gameable->player_hand) + count($gameable->player_hand2) + count($gameable->dealer_hand) + 1)->code]
                );
            }

            $gameable->dealer_score = $this->calculateScore($gameable->dealer_hand);

            $win = 0;
            // 1st hand won
            if ($gameable->player_score <= 21 && ($gameable->dealer_score > 21 || $gameable->player_score > $gameable->dealer_score)) {
                $gameable->win = $gameable->bet * 2; // win pays 1:1
                $win += $gameable->win;
            // 1st hand push
            } elseif ($gameable->player_score <= 21 && $gameable->player_score == $gameable->dealer_score) {
                $gameable->win = $gameable->bet; // return bet
                $win += $gameable->win;
            }

            // 2nd hand won
            if ($gameable->player_score2 <= 21 && ($gameable->dealer_score > 21 || $gameable->player_score2 > $gameable->dealer_score)) {
                $gameable->win2 = $gameable->bet2 * 2; // win pays 1:1
                $win += $gameable->win2;
            // 2nd hand push
            } elseif ($gameable->player_score2 <= 21 && $gameable->player_score2 == $gameable->dealer_score) {
                $gameable->win2 = $gameable->bet2; // return bet
                $win += $gameable->win2;
            }

            $gameAttributes = [
                'win' => $win,
                'status' => Game::STATUS_COMPLETED
            ];
        }

        if ($save) {
            $this->save($gameAttributes);
        }

        return $this;
    }

    /**
     * Hit one more card from the deck
     *
     * @return GameService
     * @throws \Exception
     */
    public function splitHit(int $hand): GameService
    {
        $gameable = $this->getGameable();

        // load saved deck
        $this->deck->set($gameable->deck);

        // get new card
        $newCard = $this->deck->getCard(count($gameable->player_hand) + count($gameable->player_hand2) + count($gameable->dealer_hand) + 1)->code;

        if ($hand == 1) {
            $attrHand = 'player_hand';
            $attrScore = 'player_score';
        } elseif ($hand == 2) {
            $attrHand = 'player_hand2';
            $attrScore = 'player_score2';
        }

        // hit new card depending on hand
        $gameable->{$attrHand} = array_merge($gameable->{$attrHand}, [$newCard]);
        $gameable->{$attrScore} = $this->calculateScore($gameable->{$attrHand});

        $gameable->actions = ['stand', 'hit'];
        $gameAttributes = [];

        // 1st hand played - 21 or busted
        if ($hand == 1 && $gameable->player_score >= 21) {
            $this->splitStand(1, FALSE);
        // 2nd hand played - 21 or busted
        } else if ($hand == 2 && $gameable->player_score <= 21 && $gameable->player_score2 >= 21) {
            $this->splitStand(2, FALSE);
            $gameAttributes = [
                'win' => $gameable->win + $gameable->win2,
                'status' => Game::STATUS_COMPLETED
            ];
        // 2nd hand played - both hands busted, mark game as completed, delaer doesn't need to hit more cards
        } else if ($hand == 2 && $gameable->player_score > 21 && $gameable->player_score2 > 21) {
            $gameAttributes = ['status' => Game::STATUS_COMPLETED];
        }

        $this->save($gameAttributes);

        return $this;
    }

    protected function calculateScore(array $hand): float
    {
        // possible aces values depending on their number of occurances in the hand
        $acesOutcomes = [
            1 => [1, 11], // 1 Ace
            2 => [2, 12, 22], // 2 Aces
            3 => [3, 13, 23, 33], // 3 Aces
            4 => [4, 14, 24, 34, 44], // 4 Aces
        ];
        $acesCount = 0; // count of aces in the hand
        $score = 0;

        // calculate total score for all non aces cards first
        foreach ($hand as $card) {
            // if it's not an Ace
            if ($card[1] != 'A') {
                $score += intval($card[1]) ?: 10;
            // if it's an Ace
            } else {
                $acesCount++; // increment aces count
            }
        }

        // if there are aces in the hand
        if ($acesCount) {
            // loop through possible aces outcomes (each ace can be either 1 or 11)
            foreach ($acesOutcomes[$acesCount] as $i => $acesValue) {
                $currentScore = $score;
                if ($i==0 && $acesValue + $currentScore > 21) {
                    $currentScore += $acesValue;
                    break;
                } else if ($i > 0 && $acesValue + $currentScore > 21) {
                    $currentScore += $acesOutcomes[$acesCount][$i-1];
                    break;
                } else {
                    $currentScore += $acesValue;
                }
            }
            $score = $currentScore;
        }

        return $score;
    }

    protected function addActions($target, array $actions): GameService
    {
        $target->actions = array_merge($target->actions ?: [], $actions);

        return $this;
    }

    public static function createRandomGame(User $user): void
    {
        $instance = new self($user);

        $instance->createProvablyFairGame(random_int(10000,99999));

        $minBet = intval(config('settings.bots.games.min_bet'));
        $maxBet = intval(config('settings.bots.games.max_bet'));

        $instance
            ->deal(['bet' => random_int($minBet ?: config('blackjack.min_bet'), $maxBet ?: config('blackjack.max_bet'))])
            ->unsetGameable();

        // hit until player gets 17 or more
        while($instance->getGameable()->player_score < 18) {
            $instance->hit()->unsetGameable();
        }

        // stand if not busted
        if (!$instance->isGameCompleted())
            $instance->stand();
    }
}
