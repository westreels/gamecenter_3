<?php

namespace Packages\Dice\Services;

use App\Helpers\Games\NumberGenerator;
use App\Models\Game;
use App\Models\User;
use App\Services\GameService as ParentGameService;
use App\Services\RandomGameService;
use Packages\Dice\Models\Dice;

class GameService extends ParentGameService implements RandomGameService
{
    protected $gameableClass = Dice::class;

    protected $numberGenerator;

    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->numberGenerator = new NumberGenerator();
    }

    public function makeSecret(): string
    {
        return $this->numberGenerator->generate()->getNumber();
    }

    /**
     * Deal initial set of cards
     *
     * @param $params
     */
    public function play($params): GameService
    {
        $provablyFairGame = $this->getProvablyFairGame();

        // load initial random number
        $this->numberGenerator->setNumber($provablyFairGame->secret);
        // adjust number (provably fair)
        $this->numberGenerator->shift($provablyFairGame->shift_value);

        $direction = $params['direction'];
        $bet = $params['bet'];
        $winChance = (float) round($params['win_chance'], 2);

        $gameable = new $this->gameableClass();
        $gameable->direction = $direction;
        $gameable->win_chance = $winChance;
        $gameable->payout = $this->calcPayout($winChance);
        $gameable->ref_number = $this->calcRefNumber($direction, $winChance);
        $gameable->roll = $this->numberGenerator->getNumber();

        // important to save a reference to gameable in the class property, so it can be used in the parent class
        $this->gameable = $gameable;

        $win = ($direction < 0 && $gameable->roll < $gameable->ref_number) || ($direction > 0 && $gameable->roll >= $gameable->ref_number)
            ? $bet * $gameable->payout
            : 0;

        $this->save([
            'bet' => $bet,
            'win' => $win,
            'status' => Game::STATUS_COMPLETED
        ]);

        return $this;
    }

    protected function calcPayout(float $winChance): float
    {
        return round((100 - config('dice.house_edge')) / $winChance, 4);
    }

    protected function calcRefNumber(int $direction, float $winChance): int
    {
        return $direction < 0
            ? round($winChance * 100)
            : round((100 - $winChance) * 100);
    }

    public static function createRandomGame(User $user): void
    {
        $instance = new self($user);

        $instance->createProvablyFairGame(random_int(10000,99999));

        $minBet = intval(config('settings.bots.games.min_bet'));
        $maxBet = intval(config('settings.bots.games.max_bet'));

        $instance->play([
            'bet'           => random_int($minBet ?: config('dice.min_bet'), $maxBet ?: config('dice.max_bet')),
            'direction'     => array_rand([-1, 1]),
            'win_chance'    => random_int(config('dice.min_win_chance'), config('dice.max_win_chance')),
        ]);
    }
}
