<?php

namespace Packages\Payments\Services;

use Omnipay\Omnipay;
use Packages\Payments\Helpers\Tron;

class TronPaymentService extends EthereumPaymentService
{
    protected $baseCurrency = 'TRX';

    protected function init()
    {
        $this->omnipay = Omnipay::create('Trongrid');

        $this->omnipay->initialize([
            'network' => config('payments.tron.network')
        ]);
    }

    /**
     * Get balance of a given Tron or TRC20 address
     *
     * @param string $address
     * @return PaymentService
     */
    public function fetchAddressBalance(string $address): float
    {
        if ($this->isToken()) {
            // there is no way to retrieve balance in TRC20 tokens at the moment
            return PHP_INT_MAX;
        } else {
            $this->response = $this->omnipay->fetchBalance(['address' => $address])->send();

            if ($this->isResponseSuccessful()) {
                $balance = $this->getResponseData();

                // convert balance to decimal, because it's provided in sun
                return Tron::fromSun($balance);
            }
        }
    }

    public function fetchTransaction(string $transactionId)
    {
        $this->response = $this->omnipay->fetchTransaction(['transaction_id' => $transactionId])->send();

        return $this->isResponseSuccessful() ? $this->getResponseData() : NULL;
    }
}
