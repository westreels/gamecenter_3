<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait MultiplayerGameable
{
    use StandardDateFormat;

    /**
     * Related Game model
     *
     * @return MorphOne
     */
    public function multiplayerGame(): MorphOne
    {
        return $this->morphOne(MultiplayerGame::class, 'gameable');
    }
}
