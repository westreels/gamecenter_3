<?php

namespace App\Helpers\Queries;

use App\Models\Game;
use Illuminate\Database\Eloquent\Builder;

class GameWinQuery extends Query
{
    protected $model = Game::class;

    protected $whereClauses = [['win', '>', 'bet']];

    // Load relations with specific columns (such as name) that can be safely shared with all users.
    // email is required for avatar_url to work, but it will be removed
    protected $with = ['account:id,user_id', 'account.user:id,name,email,avatar,last_seen_at'];

    public function getOrderBy(): string
    {
        return '(win - bet)';
    }

    public function getOrderDirection(): string
    {
        return 'desc';
    }

    public function getRowsToSkip(): int
    {
        return 0;
    }

    public function getItemsPerPage(): int
    {
        return 10;
    }

    public function calculateRowsCount(): int
    {
        return 10;
    }

    protected function getBaseBuilder(): Builder
    {
        return parent::getBaseBuilder()->completed();
    }
}
