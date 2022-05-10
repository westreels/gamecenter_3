<?php
/** @noinspection PhpUnhandledExceptionInspection */

namespace Packages\HorseRacing\Helpers;

use Illuminate\Support\Collection;

class RaceResult
{
    /**
     * Horses positions in a race
     *
     * @var Collection
     */
    protected $result;

    /**
     * Generate result (horses positions) in a race
     *
     * @param int $numberOfHorses
     * @return RaceResult
     * @throws \Exception
     */
    public function generate(int $numberOfHorses): RaceResult
    {
        $this->result = collect(range(0, $numberOfHorses - 1))
            ->shuffle(random_int(1, 999999));

        return $this;
    }

    /**
     * Slice $n horses from the beginning and append them to the end
     *
     * @param int $n
     * @return RaceResult
     */
    public function shift(int $n): RaceResult
    {
        $count = $this->get()->count();

        if ($n > $count) {
            $n = $n % $count;
        } elseif ($n == 0) {
            $n = $count;
        }

        // cards under cut index
        $spliced = $this->result->splice($n);

        // result will be unchanged when $n == 0 or $n == $count
        $this->result = $spliced->concat($this->result);

        return $this;
    }

    /**
     * Set result (horses positions) in a race
     *
     * @param string $result - comma delimited string of numbers
     * @return RaceResult
     */
    public function set(string $result): RaceResult
    {
        $this->result = collect(explode(',', $result))
            ->map(function ($n) {
                return (int) $n;
            });

        return $this;
    }

    /**
     * Get horses positions
     *
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->result;
    }

    /**
     * First to finish horse index
     *
     * @return int
     */
    public function first(): int
    {
        return $this->result->first();
    }

    /**
     * Second to finish horse index
     *
     * @return int
     */
    public function second(): int
    {
        return $this->result->get(1);
    }

    /**
     * Third to finish horse index
     *
     * @return int
     */
    public function third(): int
    {
        return $this->result->get(2);
    }
}
