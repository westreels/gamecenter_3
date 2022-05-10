<?php

namespace App\Helpers\Queries\Filters;

use App\Models\User;

class UserStatusFilter extends EnumFilter
{
    protected $key = 'status';
    protected $model = User::class;
    protected $table = 'users';
}
