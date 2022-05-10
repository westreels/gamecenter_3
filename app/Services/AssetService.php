<?php

namespace App\Services;

class AssetService
{
    protected $api;

    public function __construct(AssetApi $api)
    {
        $this->api = $api;
    }
}
