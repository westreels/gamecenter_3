<?php

namespace Packages\Crash\Providers;

use App\Providers\PackageServiceProvider as DefaultPackageServiceProvider;

class PackageServiceProvider extends DefaultPackageServiceProvider
{
    protected $packageId = 'crash';
}
