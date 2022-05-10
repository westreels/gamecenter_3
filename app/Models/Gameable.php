<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Gameable
{
    use StandardDateFormat;

    /**
     * Related Game model
     *
     * @return MorphOne
     */
    public function game(): MorphOne
    {
        return $this->morphOne(Game::class, 'gameable');
    }

    /**
     * Related Game models (there can be more than one in multiplayer games)
     *
     * @return MorphOne
     */
    public function games(): MorphMany
    {
        return $this->morphMany(Game::class, 'gameable');
    }

    /**
     * Getter for is_provably_fair attribute
     *
     * @return bool
     */
    public function getIsProvablyFairAttribute(): bool
    {
        return $this instanceof ProvableGame;
    }
}
