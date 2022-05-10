<?php

namespace Packages\Dice3D\Services;

use App\Helpers\Games\NumberGenerator;
use App\Models\Game;
use App\Models\User;
use App\Services\GameService as ParentGameService;
use App\Services\RandomGameService;
use Packages\Dice3D\Models\Dice3D;

class GameService extends ParentGameService implements RandomGameService
{
    protected $gameableClass = Dice3D::class;

    protected $numberGenerator;

    public function __construct(User $user)
    {
        parent::__construct($user);

        $this->numberGenerator = new NumberGenerator(self::calcMinRoll(), self::calcMaxRoll());
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
        $refNumber = $params['ref_number'];

        $gameable = new $this->gameableClass();
        $gameable->direction = $direction;
        $gameable->win_chance = $this->calcWinChance($direction, $refNumber);
        $gameable->payout = $this->calcPayout($gameable->win_chance);
        $gameable->ref_number = $refNumber;
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

    public static function createRandomGame(User $user): void
    {
        $instance = new self($user);

        $instance->createProvablyFairGame(random_int(10000,99999));

        $minBet = intval(config('settings.bots.games.min_bet'));
        $maxBet = intval(config('settings.bots.games.max_bet'));

        $instance->play([
            'bet'           => random_int($minBet ?: config('dice-3d.min_bet'), $maxBet ?: config('dice-3d.max_bet')),
            'direction'     => array_rand([-1, 1]),
            'ref_number'    => random_int(self::calcMinRefNumber(), self::calcMaxRefNumber()),
        ]);
    }

    public static function calcMinRoll(): int
    {
        return count(config('dice-3d.dice'));
    }

    public static function calcMaxRoll(): int
    {
        return array_reduce(config('dice-3d.dice'), function ($carry, $id) {
            $carry += config('dice-3d.dice_types')[$id]['max'];
            return $carry;
        }, 0);
    }

    public static function calcMinRefNumber(): int
    {
        return self::calcMinRoll() + 1;
    }

    public static function calcMaxRefNumber(): int
    {
        return self::calcMaxRoll();
    }

    protected function calcWinChance(int $direction, int $refNumber): float
    {
        $base = self::calcMaxRoll() - self::calcMinRoll() + 1;

        $winChance = $direction < 0
            ? ($refNumber - self::calcMinRoll()) / $base * 100
            : (self::calcMaxRoll() - $refNumber + 1) / $base * 100;

        return round($winChance, 2);
    }

    protected function calcPayout(float $winChance): float
    {
        return round((100 - config('dice-3d.house_edge')) / $winChance, 4);
    }
}
