<?php

namespace Packages\GameProviders\Models;

use App\Models\Gameable;
use App\Models\ProviderGameable;
use Illuminate\Database\Eloquent\Model;

class EvoplayGame extends Model
{
    use Gameable, ProviderGameable;

    protected $table = 'games_evoplay';

    protected $casts = [
        'data' => 'collection'
    ];
}
