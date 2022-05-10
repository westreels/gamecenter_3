<?php

namespace Packages\Payments\Http\Controllers;

use Packages\Payments\Helpers\Ethereum;
use Packages\Payments\Models\EthereumAddress;

class EthereumAddressController extends BlockchainAddressController
{
    protected $model = EthereumAddress::class;

    protected $helper = Ethereum::class;
}
