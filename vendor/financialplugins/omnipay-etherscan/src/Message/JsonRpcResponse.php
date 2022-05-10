<?php

namespace Omnipay\Etherscan\Message;

class JsonRpcResponse extends Response
{
    public function isSuccessful()
    {
        return isset($this->data->result) && !is_null($this->data->result) && !isset($this->data->error);
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getMessage()
    {
        return !isset($this->data->result) || is_null($this->data->result)
            ? 'No data found'
            : ($this->data->error->message ?? 'UNKNOWN');
    }
}
