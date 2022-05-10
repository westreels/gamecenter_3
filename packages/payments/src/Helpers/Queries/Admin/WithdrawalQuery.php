<?php

namespace Packages\Payments\Helpers\Queries\Admin;

use App\Helpers\Queries\Query;
use Illuminate\Database\Eloquent\Builder;
use Packages\Payments\Helpers\Queries\Filters\WithdrawalStatusFilter;
use Packages\Payments\Models\Withdrawal;

class WithdrawalQuery extends Query
{
    protected $model = Withdrawal::class;

    protected $with = ['account.user', 'method', 'method.paymentMethod', 'method.paymentMethod.gateway'];

    protected $filters = [[WithdrawalStatusFilter::class]];

    protected $searchableColumns = [['id'], 'account.user' => ['name', 'email']];

    protected $sortableColumns = [
        'id'                => 'id',
        'payment_amount'    => 'payment_amount',
        'payment_currency'  => 'payment_currency',
        'amount'            => 'amount',
        'created_ago'       => 'created_at',
    ];
}
