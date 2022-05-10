<?php

namespace App\Helpers\Queries;

use App\Helpers\Utils;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class LeaderboardQuery extends Query
{
    protected $model = User::class;

    protected $sortableColumns = [
        'name',
        'bet_count',
        'bet_total',
        'profit_total',
        'profit_max'
    ];

    protected function calculateRowsCount(): int
    {
        return $this->model::active()->count();
    }

    protected function getBaseBuilder(): Builder
    {
        return $this->model::selectRaw('users.id,
                users.name,
                users.email,
                users.avatar,
                users.last_seen_at,
                COUNT(games.id) AS bet_count,
                IFNULL(SUM(games.bet),0) AS bet_total,
                IF(hide_profit,0,IFNULL(SUM(games.win-games.bet),0)) AS profit_total,
                IF(hide_profit,0,IFNULL(MAX(games.win-games.bet),0)) AS profit_max')
            ->active()
            ->leftJoin('accounts', 'accounts.user_id', '=', 'users.id')
            ->leftJoin('games', function (JoinClause $query) {
                $query->on('accounts.id', '=', 'games.account_id');
                $query->on('games.status', '=', DB::raw(Game::STATUS_COMPLETED));
                $query->when(request()->query('period'), function ($query, $period) {
                    $query->whereBetween('games.created_at', Utils::getDateRange($period));
                });
            })
            ->groupBy('users.id', 'name', 'email', 'avatar', 'last_seen_at', 'hide_profit');
    }
}
