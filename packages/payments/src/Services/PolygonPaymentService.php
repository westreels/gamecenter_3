<?php

namespace Packages\Payments\Services;

use Omnipay\Omnipay;

class PolygonPaymentService extends EthereumPaymentService
{
    protected $baseCurrency = 'MATIC';

    protected function init()
    {
        $this->omnipay = Omnipay::create('Etherscan');

        $this->omnipay->initialize([
            'api_key' => config('payments.polygon.api_key'),
            'network' => config('payments.polygon.network')
        ]);
    }
}
