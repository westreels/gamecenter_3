<?php

namespace Omnipay\Trongrid\Message;

use Omnipay\Common\Message\AbstractResponse;

class Response extends AbstractResponse
{
    public function isSuccessful()
    {
        return !empty(array_keys((array) $this->data)) && !isset($this->data->Error);
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->data->Error ?? 'UNKNOWN';
    }

    /**
     * Get the response data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
