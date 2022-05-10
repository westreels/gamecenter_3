<?php

namespace Omnipay\Trongrid\Message;

class FetchBalanceRequest extends AbstractRequest
{
    protected $query = 'wallet/getaccount';

    protected $responseClass = FetchBalanceResponse::class;

    protected function validateRequest(): void
    {
        $this->validate(
            'address'
        );
    }

    public function getData()
    {
        return [
            'address' => $this->getAddress(),
            'visible' => TRUE
        ];
    }

    public function getAddress()
    {
        return $this->getParameter('address');
    }

    public function setAddress($value)
    {
        return $this->setParameter('address', $value);
    }
}
