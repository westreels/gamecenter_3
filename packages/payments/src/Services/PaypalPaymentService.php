<?php

namespace Packages\Payments\Services;

use Omnipay\Omnipay;

class PaypalPaymentService extends PaymentService
{
    protected $omnipay;

    protected function init()
    {
        $this->omnipay = Omnipay::create('PayPal_Express');

        $this->omnipay->initialize([
            'username'  => config('payments.paypal.user'),
            'password'  => config('payments.paypal.password'),
            'signature' => config('payments.paypal.signature'),
            'testMode'  => config('payments.paypal.test_mode'),
        ]);
    }

    public function createDeposit(array $params): PaymentService
    {
        $params = [
            'amount'        => round($params['amount'] / $this->getMethodRate(), 2),
            'currency'      => $this->getMethodCurrency(),
            'description'   => $this->getDepositDescription(),
            'returnUrl'     => $this->getDepositReturnUrl(),
            'cancelUrl'     => $this->getDepositCancelUrl(),
        ];

        $this->response = $this->omnipay->purchase($params)->send();

        return $this;
    }

    public function completeDeposit(array $params): PaymentService
    {
        $this->response = $this->omnipay->completePurchase($params)->send();

        return $this;
    }

    public function getTransactionReferenceParameterName(): string
    {
        return 'token';
    }

    public function getTransactionReference(): string
    {
        return $this->response->getTransactionReference();
    }

    public function getCompletePaymentParameters(): array
    {
        return ['token', 'PayerID'];
    }

    public function getCancelPaymentParameters(): array
    {
        return ['token'];
    }
}
