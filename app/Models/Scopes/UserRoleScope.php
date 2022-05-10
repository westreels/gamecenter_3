<?php

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait UserRoleScope
{
    public function scopeUserRole(Builder $query, ?array $roles): Builder
    {
        $column = $this->getTable() . '.role';

        return $query->when($roles, function (Builder $query, array $roles) use ($column) {
            $query->whereIn(
                $column,
                collect($roles)->map(function ($role) {
                    return constant(User::class . '::ROLE_' . strtoupper($role));
                })
                // remove NULLs (caused by non existing roles)
                ->filter()
            );
        });
    }
}
