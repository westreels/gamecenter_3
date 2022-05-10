<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Exception;

class PackageServiceProvider extends ServiceProvider
{
    protected $packageId;

    protected $packageBasePath;

    public function __construct()
    {
        parent::__construct(app());

        if (!$this->packageId) {
            throw new Exception('Package ID must be set in the child PackageServiceProvider class.');
        }

        $this->packageBasePath = base_path('packages/' . $this->packageId);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // load package config
        $this->mergeConfigFrom($this->packageBasePath . '/config/config.php', $this->packageId);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // load migrations
        $this->loadMigrationsFrom($this->packageBasePath . '/database/migrations');
        // load routes
        $this->loadRoutesFrom($this->packageBasePath . '/routes/api.php');
    }
}
