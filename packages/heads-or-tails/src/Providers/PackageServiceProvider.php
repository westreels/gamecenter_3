<?php

namespace Packages\HeadsOrTails\Providers;

use App\Providers\PackageServiceProvider as DefaultPackageServiceProvider;

class PackageServiceProvider extends DefaultPackageServiceProvider
{
    protected $packageId = 'heads-or-tails';
}
