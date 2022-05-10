<?php

namespace Packages\SicBo\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;

class SicBoBet
{
    const BET_SMALL = 'small';
    const BET_BIG = 'big';
    const BET_SINGLE = 'single';
    const BET_DOUBLE = 'double';
    const BET_TRIPLE = 'triple';
    const BET_ANY_TRIPLE = 'any_triple';
    const BET_COMBINATION = 'combination';
    const BET_TOTAL = 'total';

    protected $sicBo;
    protected $bets;

    public function __construct(SicBo $sicBo, array $bets)
    {
        $this->sicBo = $sicBo;

        $this->bets = collect($bets)
            ->map(function ($bet) {
                return (object) array_merge($bet, ['win' => $this->calculateWin((object) $bet)]);
            });
    }

    /**
     * Get bets
     *
     * @return Collection
     */
    public function getBets(): Collection
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
        return $this->bets->sum('amount');
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
        $score = $this->sicBo->getScore();
        $payout = config('sic-bo.paytable')->{$bet->type};

        if (($bet->type == self::BET_SMALL && $score >= 4 && $score <= 10)
            || ($bet->type == self::BET_BIG && $score >= 11 && $score <= 17)
            || ($bet->type == self::BET_DOUBLE && $this->sicBo->hasTwoNumbers($bet->numbers[0]))
            || ($bet->type == self::BET_TRIPLE && $this->sicBo->hasThreeNumbers($bet->numbers[0]))
            || ($bet->type == self::BET_ANY_TRIPLE && $this->sicBo->hasAnyThreeNumbers())
            || ($bet->type == self::BET_COMBINATION && $this->sicBo->hasCombination($bet->numbers))
        ) {
            return round($bet->amount * $payout, 2);
        } elseif ($bet->type == self::BET_SINGLE) {
            if ($this->sicBo->hasThreeNumbers($bet->numbers[0])) {
                return round($bet->amount * $payout[2], 2);
            } elseif ($this->sicBo->hasTwoNumbers($bet->numbers[0])) {
                return round($bet->amount * $payout[1], 2);
            } elseif ($this->sicBo->hasOneNumber($bet->numbers[0])) {
                return round($bet->amount * $payout[0], 2);
            }
        } elseif ($bet->type == self::BET_TOTAL) {
            if ($bet->numbers[0] == 10 && $score == 10 || $bet->numbers[0] == 11 && $score == 11) {
                return round($bet->amount * $payout[0], 2);
            } elseif ($bet->numbers[0] == 9 && $score == 9 || $bet->numbers[0] == 12 && $score == 12) {
                return round($bet->amount * $payout[1], 2);
            } elseif ($bet->numbers[0] == 8 && $score == 8 || $bet->numbers[0] == 13 && $score == 13) {
                return round($bet->amount * $payout[2], 2);
            } elseif ($bet->numbers[0] == 7 && $score == 7 || $bet->numbers[0] == 14 && $score == 14) {
                return round($bet->amount * $payout[3], 2);
            } elseif ($bet->numbers[0] == 6 && $score == 6 || $bet->numbers[0] == 15 && $score == 15) {
                return round($bet->amount * $payout[4], 2);
            } elseif ($bet->numbers[0] == 5 && $score == 5 || $bet->numbers[0] == 16 && $score == 16) {
                return round($bet->amount * $payout[5], 2);
            } elseif ($bet->numbers[0] == 4 && $score == 4 || $bet->numbers[0] == 17 && $score == 17) {
                return round($bet->amount * $payout[6], 2);
            }
        }

        return 0;
    }

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
