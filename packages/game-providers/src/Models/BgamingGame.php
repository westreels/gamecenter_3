<?php

namespace Packages\GameProviders\Models;

use App\Models\Gameable;
use App\Models\ProviderGameable;
use Illuminate\Database\Eloquent\Model;

class BgamingGame extends Model
{
    use Gameable, ProviderGameable;

    protected $table = 'games_bgaming';

    protected $casts = [
        'data' => 'collection'
    ];
}
