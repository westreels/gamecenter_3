<?php

namespace Packages\Payments\Http\Controllers;

use Packages\Payments\Helpers\Ethereum;
use Packages\Payments\Models\PolygonAddress;

class PolygonAddressController extends BlockchainAddressController
{
    protected $model = PolygonAddress::class;

    protected $helper = Ethereum::class;
}
