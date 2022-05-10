<?php

namespace Omnipay\Etherscan\Message;

class FetchTransactionRequest extends AbstractRequest
{
    protected $responseClass = JsonRpcResponse::class;

    protected function getModule(): string
    {
        return 'proxy';
    }

    protected function getAction(): string
    {
        return 'eth_getTransactionByHash';
    }

    protected function validateRequest(): void
    {
        $this->validate(
            'transactionReference'
        );
    }

    protected function getRequestData()
    {
        return [
            'txhash' => $this->getTransactionReference() // this method is already implemented in the parent AbstractRequest class
        ];
    }
}
