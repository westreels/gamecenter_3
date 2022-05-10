<?php

namespace Packages\HeadsOrTails\Models;

use App\Models\Gameable;
use App\Models\ProvableGame;
use Illuminate\Database\Eloquent\Model;

class HeadsOrTails extends Model implements ProvableGame
{
    use Gameable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games_heads_or_tails';

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        //
    ];

    protected $appends = ['toss_bet_title', 'result'];

    /**
     * Format $gameable->toss_bet_title attribute
     *
     * @return string
     */
    public function getTossBetTitleAttribute(): string
    {
        return $this->toss_bet == 0 ? __('Heads') : __('Tails');
    }

    /**
     * Format $gameable->result attribute
     *
     * @return string
     */
    public function getResultAttribute(): string
    {
        return $this->toss_result == 0 ? __('Heads') : __('Tails');
    }

    /**
     * Getter for secret_attribute attribute
     *
     * @return string
     */
    public function getSecretAttributeAttribute(): string
    {
        return 'toss_result';
    }

    /**
     * Getter for secret_attribute_hint attribute
     *
     * @return string
     */
    public function getSecretAttributeHintAttribute(): string
    {
        return __('The initial toss is increased by the Shift value.');
    }
}
