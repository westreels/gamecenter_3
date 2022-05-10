<?php

namespace Packages\Dice\Providers;

use App\Providers\PackageServiceProvider as DefaultPackageServiceProvider;

class PackageServiceProvider extends DefaultPackageServiceProvider
{
    protected $packageId = 'dice';
}
