<?php

namespace Packages\Raffle\Helpers\Queries;

use App\Helpers\Queries\Query;
use Packages\Raffle\Models\Raffle;

class RaffleQuery extends Query
{
    protected $model = Raffle::class;

    protected $searchableColumns = [['id', 'title'], 'winningTicket.account.user' => ['name', 'email']];

    protected $sortableColumns = [
        'id'            => 'id',
        'win'           => 'win',
        'ticket_price'  => 'ticket_price',
        'updated_ago'   => 'updated_at',
    ];
}
