<?php

namespace Packages\Slots\Helpers;

/**
 * Class SlotMachine
 * @package Packages\Slots\Helpers
 */
class SlotMachine
{
    const MIN_SPIN = 1;
    const MAX_SPIN = 999;

    /**
     * @var array - number of symbols in each reel, e.g. [7, 8, 8, 10, 8]. Array length is the total number of reels.
     */
    private $reels = [0, 0, 0, 0, 0];

    /**
     * @var array - number of spins each reel should make
     */
    private $spins = [0, 0, 0, 0, 0];

    /**
     * Randomly spin the reels
     *
     * @return SlotMachine
     */
    public function spin(): SlotMachine
    {
        $this->spins = array_map(function($symbolsCount) {
            return random_int(self::MIN_SPIN, self::MAX_SPIN);
        }, $this->spins);

        return $this;
    }

    /**
     * Shift spins values
     *
     * @param int $shift
     * @return SlotMachine
     */
    public function shift(int $shift): SlotMachine
    {
        $this->spins = array_map(function ($value) use ($shift) {
            return $value + $shift % $value;
        }, $this->spins);

        return $this;
    }

    /**
     * Get spins array
     *
     * @return array
     */
    public function getSpins(): array
    {
        return $this->spins;
    }

    public function setSpins(array $spins): SlotMachine
    {
        $this->spins = $spins;

        return $this;
    }

    /**
     * Get reels
     *
     * @return array
     */
    public function getReels(): array
    {
        return $this->reels;
    }

    /**
     * Set reels
     *
     * @param array $reels
     * @return SlotMachine
     */
    public function setReels(array $reels): SlotMachine
    {
        $this->reels = $reels;

        return $this;
    }

    /**
     * Return current reels positions according to number of spins and number of symbols per each reel
     *
     * @return array
     */
    public function getReelsPositions(): array
    {
        return array_map(function ($symbolsCount, $spinCount) {
            return $symbolsCount != $spinCount
                ? max($symbolsCount, $spinCount) % min($symbolsCount, $spinCount)
                : 0;
        }, $this->reels, $this->spins);
    }
}
