<?php

namespace App\Models;

trait ProviderGameable
{
    /**
     * Getter for title attribute
     *
     * @return string
     */
    public function getTitleAttribute(): string
    {
        return $this->name;
    }
}
