<?php

namespace Packages\MultiplayerBlackjack\Services;

use App\Helpers\Games\CardDeck;
use App\Models\User;
use App\Services\MultiroomMultiplayerGameService;
use Illuminate\Database\Eloquent\Model;
use Packages\MultiplayerBlackjack\Models\MultiplayerBlackjack;

class GameService extends MultiroomMultiplayerGameService
{
    protected $gameableClass = MultiplayerBlackjack::class;

    protected $deck;
    protected $actionDuration;
    protected $fee;

    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->deck = new CardDeck();
        $this->actionDuration = (int) config('multiplayer-blackjack.action_duration');
        $this->finalHitThreshold = (int) config('multiplayer-blackjack.final_hit_threshold');
        $this->fee = (float) config('multiplayer-blackjack.fee');
    }

    public function makeSecret(): string
    {
        return implode(',', $this->deck->toArray());
    }

    protected function createGameable(): MultiroomMultiplayerGameService
    {
        $provablyFairGame = $this->getProvablyFairGame();

        // load initially shuffled deck
        $this->deck->set(explode(',', $provablyFairGame->secret));
        // cut the deck (provably fair)
        $this->deck->cut($provablyFairGame->shift_value % 52);

        $gameable = new $this->gameableClass();
        $gameable->deck = $this->deck->toArray();
        $gameable->hands = collect();

//        TESTING
//        -------
//        1ST PLAYER BLACKJACK
//        $gameable->deck = array_merge(['DT','SA'], array_slice($gameable->deck, 2));
//
//        1ST PLAYER BLACKJACK, 2ND PLAYER BLACKJACK
//        $gameable->deck = array_merge(['DT','SA','HT','CA'], array_slice($gameable->deck, 4));
//
//        PUSH (WITHOUT HIT)
//        $gameable->deck = array_merge(['DT','ST','HT','CT'], array_slice($gameable->deck, 4));

        $gameable->save();

        $this->setGameable($gameable);

        return $this;
    }

    public function action(string $action): MultiroomMultiplayerGameService
    {
        $gameable = $this->getGameable();
        // initialize the saved deck
        $this->deck->set($gameable->deck);

        // perform game action
        $this->$action($gameable)->save();

        $this->broadcastGameEvent($this->getGameEvent($action));

        return $this;
    }

    /**
     * Join the game (get initial set of cards)
     *
     * @param Model $gameable
     * @return Model
     */
    protected function play(Model $gameable): Model
    {
        $hands = $gameable->hands;

        $n = $hands->count() == 0 ? 1 : $hands->count() * 2 + 1;
        $cards = $this->deck->getCards(2, $n)->toArray();
        $score = $this->calculateScore($cards);

        $hands->put($this->getUser()->id, [
            'bet'           => (int) $this->getRoom()->parameters->bet,
            'cards'         => $cards,
            'score'         => $score,
            'win'           => 0,
            'action_start'  => NULL,
            'action_end'    => NULL,
        ]);

        // when all players joined and started the game
        if ($hands->count() == $this->getRoom()->parameters->players_count) {
            // complete the game if someone got blackjack
            if ($hands->where('score', 21)->count() > 0) {
                $gameable->hands = $hands;
                return $this->complete($gameable);
            }

            $actionStart = time() + 1;

            $hands = $hands->map(function ($hand) use (&$actionStart) {
                $hand = array_merge($hand, [
                    'action_start'  => $actionStart,
                    'action_end'    => $actionStart + $this->actionDuration,
                ]);

                $actionStart += $this->actionDuration + 1;

                return $hand;
            });
        } else {
            // don't broadcast events with game data until all players joined
            $this->disableGameEventBroadcasting();
        }

        $gameable->hands = $hands;

        return $gameable;
    }

    /**
     * Stand - pass the action to the next player
     *
     * @param Model $gameable
     * @return Model
     */
    protected function stand(Model $gameable): Model
    {
        $user = $this->getUser();
        $time = time();
        $i = 0;

        $gameable->hands = $gameable->hands->map(function ($hand, $userId) use ($user, $time, &$i) {
            // current users stands
            if ($userId == $user->id) {
                $hand['action_start'] = NULL;
                $hand['action_end'] = NULL;
                $i++;
            // update start / end times for next users
            } elseif ($i > 0) {
                $hand['action_start'] = $time + ($i - 1) * $this->actionDuration + 1;
                $hand['action_end'] = $hand['action_start'] + $this->actionDuration;
                $i++;
            }

            return $hand;
        });

        // complete the game if current action was triggered by the last user
        return $gameable->hands->keys()->last() == $user->id ? $this->complete($gameable) : $gameable;
    }

    /**
     * Hit - get a new card
     *
     * @param Model $gameable
     * @return Model
     */
    protected function hit(Model $gameable): Model
    {
        $user = $this->getUser();
        $hands = $gameable->hands;

        $drawnCardsCount = $hands->sum(function ($hand) {
            return count($hand['cards']);
        });

        $hand = $hands->get($user->id);
        $hand['cards'] = array_merge($hand['cards'], [$this->deck->getCard($drawnCardsCount + 1)->code]);
        $hand['score'] = $this->calculateScore($hand['cards']);
        $hands->put($user->id, $hand);

        $gameable->hands = $hands;

        if (time() >= $hand['action_end'] - $this->finalHitThreshold) {
            return $this->stand($gameable);
        }

        return $gameable;
    }

    /**
     * Determine winners and complete the game
     *
     * @param Model $gameable
     * @return Model
     */
    protected function complete(Model $gameable): Model
    {
        $room = $this->getRoom();
        $hands = $gameable->hands;

        // get the max score that doesn't exceed 21 points
        $maxScore = $hands->where('score', '<=', 21)->max('score'); // $maxScore will be NULL if all players busted
        $winningHands = $maxScore ? $hands->where('score', '=', $maxScore) : collect();

        // check if someone had a blackjack
        if ($maxScore == 21 && $winningHands->count() > 1) {
            // get hands with blackjack
            $handsWithBlackjack = $winningHands->filter(function ($hand) {
                return count($hand['cards']) == 2;
            });

            // if there was a blackjack only those hands with blackjack win
            if ($handsWithBlackjack->count() > 0) {
                $winningHands = $handsWithBlackjack;
            }
        }

        $win = $winningHands->count() > 0
            ? round($room->parameters->bet * $hands->count() * (1 - $this->fee / 100) / $winningHands->count(), 2)
            : 0;

        $hands->each(function ($hand, $userId) use ($room, $hands, $winningHands, $win) {
            $player = $room->playerById($userId);

            if ($winningHands->has($userId)) {
                $hands->put($userId, array_merge($hand, ['win' => $win]));
            }

            $this->completeGame($player->game, [
                'win' => $winningHands->has($userId) ? $win : 0
            ]);
        });

        $gameable->hands = $hands;

        return $gameable;
    }

    /**
     * Cancel games for all users in the room
     *
     * @param Model $gameable
     * @return Model
     */
    protected function cancel(Model $gameable): Model
    {
        $room = $this->getRoom();
        $hands = $gameable->hands;

        $hands->each(function ($hand, $userId) use ($hands, $room) {
            $hands->put($userId, array_merge($hand, ['win' => $hand['bet']]));

            // cancel the game
            $this->cancelGame($room->playerById($userId)->game, [
                'win' => $hand['bet']
            ]);
        });

        $gameable->hands = $hands;

        return $gameable;
    }

    protected function getGameEvent(string $action): array
    {
        $gameable = $this->getGameable();

        $event = [
            'action' => $action,
            'user_id' => $this->getUser()->id,
            'game' => $this->getGame()->append('server_time')->only('is_completed', 'is_cancelled', 'server_time'),
            'gameable' => [
                'hands' => $gameable->hands
                    ->map(function ($hand) {
                        return [
                            'bet'           => $hand['bet'],
                            // show only 1st card to other players while the game is in progress
                            'cards'         => $this->isGameCompleted() || $this->isGameCancelled() ? $hand['cards'] : [$hand['cards'][0]] + array_pad([], count($hand['cards']),NULL),
                            // don't show the hand score while the game is in progress
                            'score'         => $this->isGameCompleted() || $this->isGameCancelled() ? $hand['score'] : -1,
                            // don't show the win amount score while the game is in progress
                            'win'           => $this->isGameCompleted() || $this->isGameCancelled() ? $hand['win'] : 0,
                            'action_start'  => $hand['action_start'],
                            'action_end'    => $hand['action_end'],
                        ];
                    })
                    ->toArray()
            ]
        ];

        return $event;
    }

    /**
     * Calculate score (points) for a given hand (set of cards)
     *
     * @param array $hand
     * @return float
     */
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
}
