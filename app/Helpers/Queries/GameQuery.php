<?php

namespace App\Helpers\Queries;

use App\Helpers\Queries\Filters\GameFilter;
use App\Helpers\Queries\Filters\GameResultFilter;
use App\Helpers\Queries\Filters\PeriodFilter;
use App\Helpers\Queries\Filters\UserRoleFilter;
use App\Models\Game;
use Illuminate\Database\Eloquent\Builder;

class GameQuery extends Query
{
    protected $model = Game::class;

    protected $with = ['account', 'account.user'];

    protected $filters = [[PeriodFilter::class, GameFilter::class, GameResultFilter::class], 'account.user' => [UserRoleFilter::class]];

    protected $searchableColumns = [['id'], 'account.user' => ['name', 'email']];

    protected $sortableColumns = [
        'id'            => 'id',
        'title'         => 'gameable_type',
        'bet'           => 'bet',
        'win'           => 'win',
        'profit'        => '(win - bet)',
        'created_ago'   => 'created_at',
    ];

    protected function getBaseBuilder(): Builder
    {
        return parent::getBaseBuilder()->completed();
    }
}
