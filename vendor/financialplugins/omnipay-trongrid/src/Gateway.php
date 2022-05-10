<?php

namespace Omnipay\Trongrid;

use Omnipay\Common\AbstractGateway;
use Omnipay\Trongrid\Message\FetchBalanceRequest;
use Omnipay\Trongrid\Message\FetchTokenBalanceRequest;
use Omnipay\Trongrid\Message\FetchTransactionRequest;

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Trongrid';
    }

    public function getDefaultParameters()
    {
        return [
            'network' => 'api.trongrid.io',
        ];
    }

    public function getNetwork(): string
    {
        return $this->getParameter('network');
    }

    public function setNetwork(string $value)
    {
        return $this->setParameter('network', $value);
    }

    public function fetchBalance(array $parameters = [])
    {
        return $this->createRequest(FetchBalanceRequest::class, $parameters);
    }

    public function fetchTransaction(array $parameters = [])
    {
        return $this->createRequest(FetchTransactionRequest::class, $parameters);
    }
}
