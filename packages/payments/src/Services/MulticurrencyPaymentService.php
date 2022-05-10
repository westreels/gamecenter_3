<?php

namespace Packages\Payments\Services;

use Illuminate\Support\Collection;

interface MulticurrencyPaymentService
{
    /**
     * Get payment currencies and their rates to credits
     *
     * @return array
     */
    public function fetchPaymentCurrencies(): Collection;
}
