<?php

namespace Packages\HorseRacing\Models;

use App\Models\Gameable;
use App\Models\ProvableGame;
use Illuminate\Database\Eloquent\Model;

class HorseRacing extends Model implements ProvableGame
{
    use Gameable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games_horse_racing';

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'bets'      => 'collection', // collection of objects
        'positions' => 'collection' // collection of numbers
    ];

    protected $appends = ['result', 'bet_total', 'win_total'];

    /**
     * Format $gameable->result attribute
     *
     * @return string
     */
    public function getResultAttribute(): string
    {
        return '';
    }

    /**
     * Getter for secret_attribute attribute
     *
     * @return string
     */
    public function getSecretAttributeAttribute(): string
    {
        return 'positions';
    }

    /**
     * Getter for secret_attribute_hint attribute
     *
     * @return string
     */
    public function getSecretAttributeHintAttribute(): string
    {
        return __('Each horse position is changed by the Shift value.');
    }

    /**
     * Getter for bet_total attribute
     *
     * @return float
     */
    public function getBetTotalAttribute(): float
    {
        return $this->bets->isNotEmpty()
            ? $this->bets->reduce(function ($carry, $item) {
                return $carry + $item['bet'];
            }, 0)
            : 0;
    }

    /**
     * Getter for win_total attribute
     *
     * @return float
     */
    public function getWinTotalAttribute(): float
    {
        return $this->bets->isNotEmpty()
            ? $this->bets->reduce(function ($carry, $item) {
                return $carry + $item['win'];
            }, 0)
            : 0;
    }
}
