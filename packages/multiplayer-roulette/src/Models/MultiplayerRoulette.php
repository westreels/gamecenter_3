<?php

namespace Packages\MultiplayerRoulette\Models;

use App\Models\Gameable;
use App\Models\MultiplayerGameable;
use App\Models\ProvableGame;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MultiplayerRoulette extends Model implements ProvableGame
{
    use Gameable;
    use MultiplayerGameable;

    public const BET_ZERO   = 'zero';
    public const BET_RED    = 'red';
    public const BET_BLACK  = 'black';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'games_multiplayer_roulette';

    /**
     * This format will be used when the model is serialized to an array or JSON.
     *
     * @var array
     */
    protected $casts = [
        // { account_id: { BET_ZERO: { "bet": X, "win": X}, BET_RED: { "bet": X, "win": X }, BET_BLACK: { "bet": X, "win": X } } }
        'bets' => 'collection'
    ];

    /**
     * Getter for secret_attribute attribute
     *
     * @return string
     */
    public function getSecretAttributeAttribute(): string
    {
        return 'win_number';
    }

    /**
     * Getter for secret_attribute_hint attribute
     *
     * @return string
     */
    public function getSecretAttributeHintAttribute(): string
    {
        return __('The initial roulette number is adjusted by the Shift value.');
    }

    public static function getBetTypes(): Collection
    {
        $r = new \ReflectionClass(__CLASS__);

        return collect($r->getConstants())
            ->filter(function ($value, $name) {
                return Str::startsWith($name, 'BET_');
            })
            ->values();
    }
}
