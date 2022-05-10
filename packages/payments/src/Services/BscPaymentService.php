<?php

namespace Packages\Payments\Services;

use Omnipay\Omnipay;

class BscPaymentService extends EthereumPaymentService
{
    protected $baseCurrency = 'BNB';

    protected function init()
    {
        $this->omnipay = Omnipay::create('Etherscan');

        $this->omnipay->initialize([
            'api_key' => config('payments.bsc.api_key'),
            'network' => config('payments.bsc.network')
        ]);
    }
}
