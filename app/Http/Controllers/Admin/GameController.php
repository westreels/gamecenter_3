<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Queries\GameQuery;
use App\Http\Controllers\Controller;

class GameController extends Controller
{
    public function index(GameQuery $query)
    {
        $items = $query
            ->get()
            ->map(function ($game) {
                $game->account->user->append('two_factor_auth_enabled', 'two_factor_auth_passed', 'is_admin', 'is_bot', 'is_active');
                return $game;
            });

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }
}
