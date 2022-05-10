<?php

namespace Packages\Payments\Http\Controllers;

use Packages\Payments\Helpers\Ethereum;
use Packages\Payments\Models\BscAddress;

class BscAddressController extends BlockchainAddressController
{
    protected $model = BscAddress::class;

    protected $helper = Ethereum::class;
}
