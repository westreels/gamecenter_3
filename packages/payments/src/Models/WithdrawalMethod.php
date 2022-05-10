<?php

namespace Packages\Payments\Models;

class WithdrawalMethod extends Method
{
    function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }
}
