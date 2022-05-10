<?php

namespace Packages\SicBo\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;
use Packages\SicBo\Helpers\SicBoBet;

class BetsAreValid implements Rule
{
    protected $bets;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Collection $bets)
    {
        $this->bets = $bets;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute = NULL, $value = NULL)
    {
        return $this->bets
            ->filter(function ($bet) {
                return (in_array($bet->type, [SicBoBet::BET_SMALL, SicBoBet::BET_BIG, SicBoBet::BET_ANY_TRIPLE]) && count($bet->numbers) == 0)
                    || (in_array($bet->type, [SicBoBet::BET_SINGLE, SicBoBet::BET_DOUBLE, SicBoBet::BET_TRIPLE]) && count($bet->numbers) == 1 && $bet->numbers[0] >= 1 && $bet->numbers[0] <= 6)
                    || (in_array($bet->type, [SicBoBet::BET_TOTAL]) && count($bet->numbers) == 1 && $bet->numbers[0] >= 4 && $bet->numbers[0] <= 17)
                    || (in_array($bet->type, [SicBoBet::BET_COMBINATION]) && count($bet->numbers) == 2 && $bet->numbers[0] != $bet->numbers[1] && $bet->numbers[0] >= 1 && $bet->numbers[0] <= 6 && $bet->numbers[1] >= 1 && $bet->numbers[1] <= 6);
            })
            ->count() == $this->bets->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('This bet is not allowed.');
    }
}
