<?php

namespace Packages\Keno\Models;

use App\Models\Gameable;
use App\Models\ProvableGame;
use Illuminate\Database\Eloquent\Model;

class Keno extends Model implements ProvableGame
{
    use Gameable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games_keno';

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'selected_numbers' => 'collection',
        'drawn_numbers' => 'collection',
    ];

    protected $appends = [
        'matches_count'
    ];

    /**
     * Getter for matches_count
     *
     * @return mixed
     */
    public function getMatchesCountAttribute()
    {
        return $this->selected_numbers->intersect($this->drawn_numbers)->count();
    }

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
        return 'drawn_numbers';
    }

    /**
     * Getter for secret_attribute_hint attribute
     *
     * @return string
     */
    public function getSecretAttributeHintAttribute(): string
    {
        return __('Each generated number is additionally changed by the Shift value.');
    }
}
