<?php

namespace App\Helpers\Queries\Filters;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    protected $key;
    protected $relation;

    public function __construct(?string $relation)
    {
        if (!$this->key) {
            throw new Exception('Filter key should be defined in the child class.');
        }

        $this->relation = $relation;
    }

    public function getValue()
    {
        return request()->query($this->key);
    }

    public function getQuery(Builder $query): Builder
    {
        return $this->relation
            ? $query->whereHas($this->relation, Closure::fromCallable([$this, 'buildQuery']))
            : $this->buildQuery($query);
    }

    abstract public function buildQuery(Builder $query): Builder;
}
