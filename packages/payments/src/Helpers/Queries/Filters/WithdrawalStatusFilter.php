<?php

namespace Packages\Payments\Helpers\Queries\Filters;

use App\Helpers\Queries\Filters\EnumFilter;
use Packages\Payments\Models\Withdrawal;

class WithdrawalStatusFilter extends EnumFilter
{
    protected $key = 'status';
    protected $model = Withdrawal::class;
    protected $table = 'withdrawals';
}
