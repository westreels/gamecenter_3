<?php

namespace Omnipay\Trongrid\Message;

class FetchBalanceResponse extends Response
{
    public function isSuccessful()
    {
        return isset($this->data->balance);
    }

    public function getData()
    {
        return $this->data->balance;
    }
}
