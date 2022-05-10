<?php

namespace Packages\Slots\Models;

use App\Models\Gameable;
use App\Models\ProvableGame;
use Illuminate\Database\Eloquent\Model;

class Slots extends Model implements ProvableGame
{
    use Gameable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games_slots';

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        'wins' => 'array',
        'reels' => 'array'
    ];

    protected $appends = ['result', 'win_titles'];

    /**
     * Getter for win_titles attribute
     *
     * @return array
     */
    public function getWinTitlesAttribute(): ?array
    {
        $result = NULL;

        if (!empty($this->wins)) {
            $result = [];

            if (isset($this->wins['lines'])) {
                $result = array_combine(
                    array_map(function ($key) {
                        return __('Line :n', ['n' => ++$key]);
                    }, array_keys($this->wins['lines'])),
                    array_column($this->wins['lines'], 'win')
                );
            }

            if (isset($this->wins['scatters'])) {
                $result[__('Scatters')] = $this->wins['scatters']['win'];
            }
        }

        return $result;
    }

    /**
     * Format $gameable->result attribute
     *
     * @return string
     */
    public function getResultAttribute(): string
    {
        return $this->win_titles ? implode(', ', $this->win_titles) : __('Nothing');
    }

    /**
     * Getter for secret_attribute attribute
     *
     * @return string
     */
    public function getSecretAttributeAttribute(): string
    {
        return 'reels';
    }

    /**
     * Getter for secret_attribute_hint attribute
     *
     * @return string
     */
    public function getSecretAttributeHintAttribute(): string
    {
        return __('Each reel is spinned N extra times, where N corresponds to a digit in the Shift value at the i-th position.');
    }
}
