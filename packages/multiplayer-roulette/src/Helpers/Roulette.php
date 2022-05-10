<?php

namespace Packages\MultiplayerRoulette\Helpers;

class Roulette
{
    protected $roulette = [
        // GREEN if array index is equal to 0
        // RED if array index is an ODD number
        // BLACK if array index is an EVEN number
        0,32,15,19,4,21,2,25,17,34,6,27,13,36,11,30,8,23,10,5,24,16,33,1,20,14,31,9,22,18,29,7,28,12,35,3,26
    ];

    // roulette ball position (index)
    private $position;

    public function __construct()
    {
        $this->position = 0;

        return $this;
    }

    /**
     * Spin the roulette wheel
     *
     * @return Roulette
     */
    public function spin(): Roulette
    {
        $this->position = random_int(0, 36);

        return $this;
    }

    /**
     * Shift roulette position by a specific number
     *
     * @param array $shifts
     * @return Roulette
     */
    public function shift(int $number): Roulette
    {
        $this->position = $this->position + $number < 37 ?
            $this->position + $number :
            ($this->position + $number) % 37;

        return $this;
    }

    /**
     * Return current roulette ball position
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Set current roulette ball position
     *
     * @param int $position
     * @return Roulette
     */
    public function setPosition(int $position): Roulette
    {
        if ($position >= 0 && $position < 37)
            $this->position = $position;

        return $this;
    }

    /**
     * Get the number at the specific position
     *
     * @return int
     */
    public function getNumber(): int
    {
        return $this->roulette[$this->getPosition()];
    }

    /**
     * Set roulette position, which corresponds to a specific number
     *
     * @param int $number
     * @return Roulette
     */
    public function setNumber(int $number): Roulette
    {
        $this->setPosition(array_search($number, $this->roulette, TRUE));

        return $this;
    }
}
