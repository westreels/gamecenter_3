<?php

namespace Packages\Payments\Helpers\Queries\Admin;

use App\Helpers\Queries\Query;
use Packages\Payments\Helpers\Queries\Filters\DepositStatusFilter;
use Packages\Payments\Models\Deposit;

class DepositQuery extends Query
{
    protected $model = Deposit::class;

    protected $with = ['account.user', 'method', 'method.paymentMethod', 'method.paymentMethod.gateway'];

    protected $filters = [[DepositStatusFilter::class]];

    protected $searchableColumns = [['id'], 'account.user' => ['name', 'email']];

    protected $sortableColumns = [
        'id'                => 'id',
        'payment_amount'    => 'payment_amount',
        'payment_currency'  => 'payment_currency',
        'amount'            => 'amount',
        'created_ago'       => 'created_at',
    ];
}
