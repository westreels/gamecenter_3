<?php

namespace App\Helpers\Queries;

use App\Helpers\Queries\Filters\PeriodFilter;
use App\Models\AccountTransaction;

class AccountTransactionQuery extends Query
{
    protected $model = AccountTransaction::class;

    protected $with = ['transactionable'];

    protected $filters = [[PeriodFilter::class]];

    protected $sortableColumns = [
        'id'            => 'id',
        'amount'        => 'amount',
        'balance'       => 'balance',
        'created_ago'   => 'created_at'
    ];
}
