<?php

namespace Omnipay\Etherscan\Message;

use Omnipay\Common\Message\AbstractResponse;

class Response extends AbstractResponse
{
    public function isSuccessful()
    {
        return isset($this->data->status)
            && isset($this->data->message)
            && $this->data->status == '1'
            && $this->data->message == 'OK';
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getMessage()
    {
        return ($this->data->message ?? 'UNKNOWN') . ' - ' . ($this->data->result ?? 'Unknown');
    }

    /**
     * Get the response data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data->result;
    }
}
