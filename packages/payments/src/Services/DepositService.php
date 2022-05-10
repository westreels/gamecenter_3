<?php

namespace Packages\Payments\Services;

use Packages\Payments\Models\Deposit;

class DepositService
{
    /**
     * Create a new deposit
     *
     * @param array $params
     * @return Deposit
     */
    public static function create(array $params): Deposit
    {
        $deposit = new Deposit();
        $deposit->account()->associate($params['account']);
        $deposit->method()->associate($params['method']);
        $deposit->external_id           = $params['external_id'] ?? NULL;
        $deposit->amount                = $params['amount'];
        $deposit->payment_amount        = $params['payment_amount'] ?? NULL;
        $deposit->payment_currency      = $params['payment_currency'] ?? NULL;
        $deposit->parameters            = $params['parameters'] ?? NULL;
        $deposit->response              = $params['response'] ?? NULL;
        $deposit->status                = $params['status'] ?? Deposit::STATUS_CREATED;
        $deposit->save();

        return $deposit;
    }
}
