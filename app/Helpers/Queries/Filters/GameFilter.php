<?php

namespace App\Helpers\Queries\Filters;

use App\Helpers\PackageManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class GameFilter extends Filter
{
    protected $key = 'game';

    public function buildQuery(Builder $query): Builder
    {
        $packageManager = App::make(PackageManager::class);

        return $query->when($packageManager->get($this->getValue()), function (Builder $query, $package) {
            if ($package->enabled) {
                $query->where('gameable_type', $package->model);
            }
        });
    }
}
