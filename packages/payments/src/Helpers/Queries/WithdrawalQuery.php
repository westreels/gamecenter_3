<?php

namespace Packages\Payments\Helpers\Queries;

use App\Helpers\Queries\Filters\PeriodFilter;
use App\Helpers\Queries\Query;
use Illuminate\Database\Eloquent\Builder;
use Packages\Payments\Helpers\Queries\Filters\WithdrawalStatusFilter;
use Packages\Payments\Models\Withdrawal;

class WithdrawalQuery extends Query
{
    protected $model = Withdrawal::class;

    protected $with = ['method:id,name'];

    protected $filters = [[PeriodFilter::class, WithdrawalStatusFilter::class]];

    protected $sortableColumns = [
        'id'                => 'id',
        'payment_amount'    => 'payment_amount',
        'payment_currency'  => 'payment_currency',
        'amount'            => 'amount',
        'created_at'        => 'created_at',
    ];
}
