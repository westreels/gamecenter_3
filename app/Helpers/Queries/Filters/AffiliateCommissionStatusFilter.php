<?php

namespace App\Helpers\Queries\Filters;

use App\Models\AffiliateCommission;

class AffiliateCommissionStatusFilter extends EnumFilter
{
    protected $key = 'status';
    protected $model = AffiliateCommission::class;
    protected $table = 'affiliate_commissions';
}
