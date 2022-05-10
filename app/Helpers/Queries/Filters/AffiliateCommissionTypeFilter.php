<?php

namespace App\Helpers\Queries\Filters;

use App\Models\AffiliateCommission;

class AffiliateCommissionTypeFilter extends EnumFilter
{
    protected $key = 'type';
    protected $model = AffiliateCommission::class;
    protected $table = 'affiliate_commissions';
}
