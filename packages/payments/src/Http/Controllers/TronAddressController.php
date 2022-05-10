<?php

namespace Packages\Payments\Http\Controllers;

use Packages\Payments\Helpers\Tron;
use Packages\Payments\Models\TronAddress;

class TronAddressController extends BlockchainAddressController
{
    protected $model = TronAddress::class;

    protected $helper = Tron::class;
}
