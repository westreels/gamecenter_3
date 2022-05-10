<?php

namespace Packages\Plinko\Helpers;

use Illuminate\Support\Collection;

class Plinko
{
    protected $path;

    public function __construct(int $rowsCount)
    {
        $this->path = collect()->pad($rowsCount, NULL);

        return $this;
    }

    /**
     * Get number of rows in the game
     *
     * @return int
     */
    public function getRowsCount(): int
    {
        return $this->path->count();
    }

    /**
     * Get ball path
     *
     * @return Collection
     */
    public function getPath(): Collection
    {
        return $this->path;
    }

    /**
     * Set ball path
     *
     * @param array $numbers
     * @return Plinko
     */
    public function setPath(array $numbers): Plinko
    {
        $this->path = $this->path->transform(function ($item, $i) use($numbers) {
            return (int) $numbers[$i];
        });

        return $this;
    }

    /**
     * Generate random ball path
     *
     * @return Plinko
     */
    public function generatePath(): Plinko
    {
        $this->path->transform(function ($item) {
            return random_int(0, 1);
        });

        return $this;
    }

    /**
     * Shift ball path
     *
     * @param int $number
     * @return Plinko
     */
    public function shiftPath(int $number): Plinko
    {
        $rowsCount = $this->getRowsCount();
        $number = $number > 0 && $number >= $rowsCount ? $number % $rowsCount : $rowsCount;

        $chunk = $this->path->splice($number);
        $this->path = $chunk->concat($this->path);

        return $this;
    }
}
