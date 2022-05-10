<?php

namespace Packages\Payments\Models;

class DepositMethod extends Method
{
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }
}
