<?php

namespace Omnipay\Trongrid\Message;

class FetchTransactionRequest extends AbstractRequest
{
    protected $query = 'wallet/gettransactionbyid';

    protected function validateRequest(): void
    {
        $this->validate(
            'transaction_id'
        );
    }

    public function getData()
    {
        return [
            'value' => $this->getTransactionId()
        ];
    }

    public function getTransactionId()
    {
        return $this->getParameter('transaction_id');
    }

    public function setTransactionId($value)
    {
        return $this->setParameter('transaction_id', $value);
    }
}
