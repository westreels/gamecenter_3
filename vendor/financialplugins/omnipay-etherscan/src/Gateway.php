<?php

namespace Omnipay\Etherscan;

use Omnipay\Common\AbstractGateway;
use Omnipay\Etherscan\Message\FetchBalanceRequest;
use Omnipay\Etherscan\Message\FetchTokenBalanceRequest;
use Omnipay\Etherscan\Message\FetchTransactionRequest;

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Etherscan';
    }

    public function getDefaultParameters()
    {
        return [
            'api_key' => '',
            'network' => '',
        ];
    }

    public function getApiKey(): string
    {
        return $this->getParameter('api_key');
    }

    public function setApiKey(string $value)
    {
        return $this->setParameter('api_key', $value);
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

    public function fetchTokenBalance(array $parameters = [])
    {
        return $this->createRequest(FetchTokenBalanceRequest::class, $parameters);
    }

    public function fetchTransaction(array $parameters = [])
    {
        return $this->createRequest(FetchTransactionRequest::class, $parameters);
    }
}
