<?php

namespace Packages\Installer\Providers;

use App\Providers\PackageServiceProvider as DefaultPackageServiceProvider;

class PackageServiceProvider extends DefaultPackageServiceProvider
{
    protected $packageId = 'installer';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // load routes
        $this->loadRoutesFrom($this->packageBasePath . '/routes/web.php');
        // load views
        $this->loadViewsFrom($this->packageBasePath . '/resources/views', 'installer');
    }
}
