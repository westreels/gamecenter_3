<?php

namespace App\Models;

interface PercentageAffiliateCommission
{
    /**
     * Get base amount for affiliate commission calculation.
     * It can be game bet amount, deposit amount etc
     *
     * @return float
     */
    public function getAffiliateCommissionBaseAmount(): float;
}
