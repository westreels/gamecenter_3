<?php

namespace Packages\Keno\Providers;

use App\Providers\PackageServiceProvider as DefaultPackageServiceProvider;

class PackageServiceProvider extends DefaultPackageServiceProvider
{
    protected $packageId = 'keno';
}
