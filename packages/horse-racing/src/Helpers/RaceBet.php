<?php

namespace Packages\HorseRacing\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;

class RaceBet
{
    public const BET_WIN = 0;
    public const BET_PLACE = 1;
    public const BET_SHOW = 2;
//    public const BET_EXACTA = 3;
//    public const BET_TRIFECTA = 4;
//    public const BET_SUPERFECTA = 5;

    protected $raceResult;
    protected $bets;
    protected $paytable;

    public function __construct(RaceResult $raceResult, array $paytable, array $bets)
    {
        $this->raceResult = $raceResult;

        $this->paytable = $paytable;

        $this->bets = collect($bets)
            ->map(function ($bet) {
                return (object) array_merge($bet, ['win' => $this->calculateWin((object) $bet)]);
            });
    }

    /**
     * @return static
     */
    public function getBets()
    {
        return $this->bets;
    }

    /**
     * Get total bet amount
     *
     * @return float
     */
    public function getBetAmount(): float
    {
        return $this->bets->sum('bet');
    }

    /**
     * Get total win amount
     *
     * @return float
     */
    public function getWinAmount(): float
    {
        return $this->bets->sum('win');
    }

    /**
     * Calculate win for a given bet
     *
     * @param object $bet
     * @return float
     */
    protected function calculateWin(object $bet): float
    {
        // Correctly guess the horse that finishes first
        if ($bet->type == self::BET_WIN) {
            return $this->raceResult->first() == $bet->positions[0]
                ? round($bet->bet * $this->paytable[self::BET_WIN][$bet->positions[0]], 2)
                : 0;
        // Correctly guess the horse that finishes first or second
        } elseif ($bet->type == self::BET_PLACE) {
            return in_array($bet->positions[0], [$this->raceResult->first(), $this->raceResult->second()])
                ? round($bet->bet * $this->paytable[self::BET_PLACE][$bet->positions[0]], 2)
                : 0;
        // Correctly guess the horse that finishes first, second or third
        } elseif ($bet->type == self::BET_SHOW) {
            return in_array($bet->positions[0], [$this->raceResult->first(), $this->raceResult->second(), $this->raceResult->third()])
                ? round($bet->bet * $this->paytable[self::BET_SHOW][$bet->positions[0]], 2)
                : 0;
        }

        return 0;
    }

    /**
     * Get bet types (collection of integer numbers)
     *
     * @return Collection
     * @throws \ReflectionException
     */
    public static function getBetTypes(): Collection
    {
        $r = new ReflectionClass(__CLASS__);

        return collect($r->getConstants())
            ->filter(function ($value, $name) {
                return Str::startsWith($name, 'BET_');
            })
            ->values();
    }
}
