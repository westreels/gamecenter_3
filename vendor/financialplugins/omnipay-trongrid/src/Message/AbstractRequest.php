<?php

namespace Omnipay\Trongrid\Message;

use Omnipay\Common\Message\AbstractRequest as OmnipayAbstractRequest;

abstract class AbstractRequest extends OmnipayAbstractRequest
{
    protected $query;

    protected $responseClass = Response::class;

    abstract protected function validateRequest(): void;

    public function getApiUrl(): string
    {
        return 'https://' . $this->getNetwork() . '/' . $this->query;
    }

    public function getNetwork(): string
    {
        return $this->getParameter('network');
    }

    public function setNetwork(string $value)
    {
        return $this->setParameter('network', $value);
    }

    public function sendData($data)
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $httpResponse = $this->httpClient->request('POST', $this->getApiUrl(), $headers, json_encode($data));

        return $this->createResponse(json_decode($httpResponse->getBody()->getContents()));
    }

    protected function createResponse($data): Response
    {
        return $this->response = new $this->responseClass($this, $data);
    }
}
