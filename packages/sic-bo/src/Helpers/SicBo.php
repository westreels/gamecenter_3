<?php

namespace Packages\SicBo\Helpers;

use App\Helpers\Games\NumberGenerator;
use Exception;
use Illuminate\Support\Collection;

class SicBo
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $dice;

    public function __construct()
    {
        $this->dice = collect([
            new NumberGenerator(1, 6),
            new NumberGenerator(1, 6),
            new NumberGenerator(1, 6),
        ]);

        return $this;
    }

    public function rollDice(): SicBo
    {
        $this->dice
            ->each(function (NumberGenerator $ng) {
                $ng->generate();
            });

        return $this;
    }

    public function getScore(): int
    {
        return $this->getDice()->sum();
    }

    public function getDice(): Collection
    {
        return $this->dice
            ->map(function (NumberGenerator $ng) {
                return $ng->getNumber();
            });
    }

    public function setDice(array $numbers): SicBo
    {
        $this->dice
            ->each(function (NumberGenerator $ng, $i) use ($numbers) {
                $ng->setNumber($numbers[$i]);
            });

        return $this;
    }

    public function shiftDice(int $number): SicBo
    {
        $this->dice
            ->each(function (NumberGenerator $ng, $i) use ($number) {
                $ng->shift($number);
            });

        return $this;
    }

    public function hasOneNumber(int $number): bool
    {
        return $this->getDice()->contains($number);
    }

    public function hasTwoNumbers(int $number): bool
    {
        return $this->getDice()
            ->filter(function ($n) use ($number) {
                return $n == $number;
            })
            ->count() == 2;
    }

    public function hasThreeNumbers(int $number): bool
    {
        return $this->getDice()
            ->filter(function ($n) use ($number) {
                return $n == $number;
            })
            ->count() == 3;
    }

    public function hasAnyThreeNumbers(): bool
    {
        return $this->getDice()->get(0) == $this->getDice()->get(1)
            && $this->getDice()->get(1) == $this->getDice()->get(2);
    }

    public function hasCombination(array $numbers): bool
    {
        if (count($numbers) != 2) {
            throw new Exception('Combination should consist of exactly 2 numbers.');
        }

        return $numbers[0] != $numbers[1] && $this->hasOneNumber($numbers[0]) && $this->hasOneNumber($numbers[1]);
    }
}
