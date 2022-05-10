<?php

namespace Packages\Slots3D\Helpers;

use Exception;
use Illuminate\Support\Collection;

/**
 * Class SlotMachine
 * @package Packages\Slots3D\Helpers
 */
class SlotMachine
{
    /**
     * @var Collection - number of symbols in each reel, e.g. [7, 8, 8]. Collection length is the total number of reels.
     */
    protected $reels;

    /**
     * @var Collection - reel positions as a collection of symbol indexes
     */
    protected $positions;

    public function __construct()
    {
        $this->positions = collect()->pad(3, 0);
    }

    /**
     * Set reels
     *
     * @param  array  $reels
     * @return $this
     */
    public function setReels(array $reels): SlotMachine
    {
        $this->reels = collect($reels)->transform(function (array $symbols) {
            return collect($symbols);
        });

        return $this;
    }

    /**
     * Randomly spin the reels
     *
     * @return SlotMachine
     */
    public function spin(): SlotMachine
    {
        if (!$this->reels) {
            throw new Exception('Slot reels should be initialized before spinning');
        }

        $this->positions->transform(function ($position, $i) {
            return random_int(0, $this->reels->get($i)->count() - 1);
        });

        return $this;
    }

    /**
     * Shift each reel N times
     *
     * @param int $shift
     * @return SlotMachine
     */
    public function shift(int $shift): SlotMachine
    {
        $this->positions->transform(function ($position, $i) use ($shift) {
            $symbolsCount = $this->reels->get($i)->count();
            return ($position + $shift) % $symbolsCount;
        });

        return $this;
    }

    /**
     * Get reels positions
     *
     * @return Collection
     */
    public function getPositions(): Collection
    {
        return $this->positions;
    }

    /**
     * Set reels positions
     *
     * @param  array  $positions
     */
    public function setPositions(array $positions): SlotMachine
    {
        $this->positions = collect($positions)->transform(function ($i) {
            return (int) $i;
        });

        return $this;
    }
}
