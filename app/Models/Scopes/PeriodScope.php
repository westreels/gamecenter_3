<?php

namespace App\Models\Scopes;

use App\Helpers\Utils;
use Illuminate\Database\Eloquent\Builder;

trait PeriodScope
{
    public function getScopePeriodColumnName()
    {
        return 'created_at';
    }

    public function scopePeriod(Builder $query, ?string $period): Builder
    {
        $column = $this->getTable() . '.' . $this->getScopePeriodColumnName();

        return $query->when($period, function (Builder $query, ?string $period) use ($column) {
            $query->whereBetween($column, Utils::getDateRange($period));
        });
    }
}
