<?php

namespace Omnipay\Etherscan\Message;

class FetchTokenBalanceRequest extends AbstractRequest
{
    protected function getModule(): string
    {
        return 'account';
    }

    protected function getAction(): string
    {
        return 'tokenbalance';
    }

    protected function validateRequest(): void
    {
        $this->validate(
            'address',
            'contract_address'
        );
    }

    protected function getRequestData()
    {
        return [
            'tag'               => 'latest',
            'address'           => $this->getAddress(),
            'contractaddress'   => $this->getContractAddress(),
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

    public function getContractAddress()
    {
        return $this->getParameter('contract_address');
    }

    public function setContractAddress($value)
    {
        return $this->setParameter('contract_address', $value);
    }
}
