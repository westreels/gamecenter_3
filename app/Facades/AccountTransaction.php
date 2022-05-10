<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AccountTransaction extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'account_transaction';
    }
}
