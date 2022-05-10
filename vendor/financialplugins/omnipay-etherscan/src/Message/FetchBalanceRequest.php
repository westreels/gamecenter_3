<?php

namespace Omnipay\Etherscan\Message;

class FetchBalanceRequest extends AbstractRequest
{
    protected function getModule(): string
    {
        return 'account';
    }

    protected function getAction(): string
    {
        return 'balance';
    }

    protected function validateRequest(): void
    {
        $this->validate(
            'address'
        );
    }

    protected function getRequestData()
    {
        return [
            'tag'       => 'latest',
            'address'   => $this->getAddress()
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
