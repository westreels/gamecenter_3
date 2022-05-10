<?php

namespace Packages\Raffle\Helpers\Queries;

use App\Helpers\Queries\Query;
use Packages\Raffle\Models\RaffleTicket;

class RaffleTicketQuery extends Query
{
    protected $model = RaffleTicket::class;
}
