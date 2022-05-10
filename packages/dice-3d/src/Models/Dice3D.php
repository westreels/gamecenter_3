<?php

namespace Packages\Dice3D\Models;

use App\Models\Gameable;
use App\Models\ProvableGame;
use Illuminate\Database\Eloquent\Model;
use Packages\Dice3D\Services\GameService;

class Dice3D extends Model implements ProvableGame
{
    use Gameable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games_dice_3d';

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        //
    ];

    protected $appends = ['result'];

    /**
     * Format $gameable->result attribute
     *
     * @return string
     */
    public function getResultAttribute(): string
    {
        return $this->roll . ' (' . $this->direction < 0 ? GameService::calcMinRoll() . ' - ' . ($this->ref_number - 1) : $this->ref_number . ' - ' . GameService::calcMaxRoll(). ')';
    }

    /**
     * Getter for secret_attribute attribute
     *
     * @return string
     */
    public function getSecretAttributeAttribute(): string
    {
        return 'roll';
    }

    /**
     * Getter for secret_attribute_hint attribute
     *
     * @return string
     */
    public function getSecretAttributeHintAttribute(): string
    {
        return __('The initial roll is increased by the Shift value.');
    }
}
