<?php

namespace Packages\Baccarat\Services;

use App\Helpers\Games\CardDeck;
use App\Models\Game;
use App\Models\User;
use App\Services\GameService as ParentGameService;
use App\Services\RandomGameService;
use Packages\Baccarat\Models\Baccarat;

class GameService extends ParentGameService implements RandomGameService
{
    protected $gameableClass = Baccarat::class;

    protected $deck;

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
    public function play($params): GameService
    {
        $provablyFairGame = $this->getProvablyFairGame();

        // load initially shuffled deck
        $this->deck->set(explode(',', $provablyFairGame->secret));
        // cut the deck (provably fair)
        $this->deck->cut($provablyFairGame->shift_value % 52);

        $gameable = new $this->gameableClass();
        $gameable->deck = $this->deck->toArray();
        $gameable->player_bet = $params['player_bet'] ?: 0;
        $gameable->banker_bet = $params['banker_bet'] ?: 0;
        $gameable->tie_bet = $params['tie_bet'] ?: 0;
        $gameable->player_hand = [$this->deck->getCard(1)->code, $this->deck->getCard(3)->code];
        $gameable->banker_hand = [$this->deck->getCard(2)->code, $this->deck->getCard(4)->code];

//        TESTING
//        -------
//        TIE (2 CARDS)
//        $gameable->player_hand = ['S6','H2'];
//        $gameable->banker_hand = ['D5','D3'];
//
//        ---------------------------------------------------------------------

        $gameable->player_score = self::calculateHandScore($gameable->player_hand);
        $gameable->banker_score = self::calculateHandScore($gameable->banker_hand);

        // important to save a reference to gameable in the class property, so it can be used in the parent class
        $this->gameable = $gameable;

        //  If neither hand has eight or nine, the drawing rules are applied to determine whether the player should receive a third card.
        if ($gameable->player_score < 8 && $gameable->banker_score < 8) {
            // If the player has total of 5 or less, the player automatically hits
            if ($gameable->player_score <= 5) {
                $gameable->player_hand = array_merge($gameable->player_hand, [$this->deck->getCard(5)->code]);
            }

            // If the player stands, the banker hits on a total of 5 or less.
            if (($gameable->player_score == 6 || $gameable->player_score == 7) && $gameable->banker_score <= 5) {
                $gameable->banker_hand = array_merge($gameable->banker_hand, [$this->deck->getCard(5)->code]);
            // If the player gets the third card then the banker draws a third card according to the following rules:
            } elseif (count($gameable->player_hand) == 3) {
                // Banker has total of 0, 1, 2: Banker always draws a third card.
                if ($gameable->banker_score <= 2
                    // If the banker total is 3, then the banker draws a third card unless the player's third card was an 8.
                    || $gameable->banker_score == 3 && $gameable->player_hand[2][1] != 8
                    // If the banker total is 4, then the banker draws a third card if the player's third card was 2, 3, 4, 5, 6, 7.
                    || $gameable->banker_score == 4 && in_array((int)$gameable->player_hand[2][1], [2, 3, 4, 5, 6, 7])
                    // If the banker total is 5, then the banker draws a third card if the player's third card was 4, 5, 6, or 7.
                    || $gameable->banker_score == 5 && in_array((int)$gameable->player_hand[2][1], [4, 5, 6, 7])
                    // If the banker total is 6, then the banker draws a third card if the player's third card was a 6 or 7.
                    || $gameable->banker_score == 6 && in_array((int)$gameable->player_hand[2][1], [6, 7])) {
                    $gameable->banker_hand = array_merge($gameable->banker_hand, [$this->deck->getCard(6)->code]);
                }
            }
        }

        $gameable->player_score = self::calculateHandScore($gameable->player_hand);
        $gameable->banker_score = self::calculateHandScore($gameable->banker_hand);

        if ($gameable->player_score > $gameable->banker_score && $gameable->player_bet > 0) {
            $gameable->player_win = $gameable->player_bet * (float) config('baccarat.payouts.player');
        }

        if ($gameable->player_score < $gameable->banker_score && $gameable->banker_bet > 0) {
            $gameable->banker_win = $gameable->banker_bet * (float) config('baccarat.payouts.banker');
        }

        if ($gameable->player_score == $gameable->banker_score && $gameable->tie_bet) {
            $gameable->tie_win = $gameable->tie_bet * (float) config('baccarat.payouts.tie');
        }

        $this->save([
            'bet'       => $gameable->player_bet + $gameable->banker_bet + $gameable->tie_bet,
            'win'       => $gameable->player_win + $gameable->banker_win + $gameable->tie_win,
            'status'    => Game::STATUS_COMPLETED
        ]);

        return $this;
    }

    /**
     * Calculate hand score
     *
     * @param array $hand
     * @return int
     */
    public static function calculateHandScore(array $hand): int
    {
        $score = 0;

        $getCardScore = function ($cardValue) {
            if (intval($cardValue) > 0)
                return intval($cardValue);
            // aces have score of 1
            elseif ($cardValue == 'A')
                return 1;
            //  jack, queen, and king are worth zero
            else
                return 0;
        };

        // loop through cards and calculate score
        foreach ($hand as $card) {
            $score += $getCardScore(substr($card, 1, 1));
        }

        // if score is >= 10 return only the right digit
        return $score < 10 ? $score : (int) substr($score, 1, 1);
    }

    public static function createRandomGame(User $user): void
    {
        $instance = new self($user);

        $instance->createProvablyFairGame(random_int(10000,99999));

        $minBet = intval(config('settings.bots.games.min_bet'));
        $maxBet = intval(config('settings.bots.games.max_bet'));

        $playerBet = random_int(0, 1);
        $bankerBet = random_int(0, 1);
        $tieBet = random_int(0, 1);

        if (!$playerBet && !$bankerBet && !$tieBet) {
            $playerBet = 1;
        }

        $instance->play([
            'player_bet' => $playerBet ? random_int($minBet ?: config('baccarat.min_bet'), $maxBet ?: config('baccarat.max_bet')) : NULL,
            'banker_bet' => $bankerBet ? random_int($minBet ?: config('baccarat.min_bet'), $maxBet ?: config('baccarat.max_bet')) : NULL,
            'tie_bet' => $tieBet ? random_int($minBet ?: config('baccarat.min_bet'), $maxBet ?: config('baccarat.max_bet')) : NULL,
        ]);
    }
}
