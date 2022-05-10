<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class PackageManager - manage extra packages (add-ons)
 *
 * @package App\Helpers
 */
class PackageManager
{
    private $packages = [];
    private $appDirectory;

    function __construct()
    {
        $this->appDirectory = __DIR__ . '/../../';

        // loop through packages definitions and load config from composer.json
        foreach (glob($this->appDirectory . 'packages/*/config.json') as $configFile) {
            $this->load($configFile);
        }

        return $this;
    }

    /**
     * Load package configuration
     *
     * @param  string  $configFile
     */
    public function load(string $configFile): PackageManager
    {
        if ($package = json_decode(file_get_contents($configFile))) {
            $this->packages[$package->id] = $package;
            $this->packages[$package->id]->code = env($this->getCodeVariable($package->id));
            $this->packages[$package->id]->code_required = in_array($package->type, ['game', 'add-on', 'prediction', 'provider']) && $package->id != 'baccarat';
            $this->packages[$package->id]->type = $package->type;
            $this->packages[$package->id]->model = sprintf('%sModels\\%s', $package->namespace, Str::studly($package->id));
            $this->packages[$package->id]->installed = $this->installed($package->id);
            $this->packages[$package->id]->enabled = $this->enabled($package->id);
            $this->packages[$package->id]->min_app_version_installed = version_compare(config('app.version'), $package->min_app_version, '>=');
        }

        return $this;
    }

    /**
     * Initialize extra attributes for each enabled package from the package config file.
     * This can only be called after other service providers are loaded.
     *
     * @return PackageManager
     */
    public function initAttributes(): PackageManager
    {
        foreach ($this->getEnabled() as $package) {
            $package->version = config($package->id . '.version');
            $package->name = __($package->name);
        }

        return $this;
    }

    /**
     * Get package by its ID
     *
     * @param $id
     * @return null
     */
    public function get($id)
    {
        return $this->packages[$id] ?? NULL;
    }

    /**
     * Get package property
     *
     * @param $id
     * @param $property
     * @return null
     */
    public function getPackageProperty($id, $property)
    {
        $package = $this->get($id);
        return $package ? ($package->{$property} ?? NULL) : NULL;
    }

    /**
     * Get all supported packages
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->packages;
    }


    /**
     * Get all installed packages
     *
     * @return array
     */
    public function getInstalled(): array
    {
        return array_filter($this->packages, function($package) {
            return $package->installed;
        });
    }

    /**
     * Get all enabled packages
     *
     * @return array
     */
    public function getEnabled(): array
    {
        return array_filter($this->packages, function($package) {
            return $package->enabled;
        });
    }

    /**
     * Check whether given package is installed
     *
     * @param $id
     * @return bool
     */
    public function installed($id): bool
    {
        $package = $this->get($id);
        return $package && file_exists($this->appDirectory . 'packages/' . $package->base_path . '/' . $package->source_path . '/' . str_replace([$package->namespace, '\\'], ['','/'], $package->providers[0]) . '.php');
    }

    /**
     * Check whether given package is enabled
     *
     * @param $id
     * @return bool
     */
    public function enabled($id): bool
    {
        $package = $this->get($id);

        return $package && $this->installed($id) &&
            isset($package->min_app_version) &&
            version_compare(config('app.version'), $package->min_app_version, '>=') &&
            !Storage::disk('local')->exists(sprintf('packages/%s/disabled', $id)) &&
            (env($this->getCodeVariable($id)) || !$package->code_required);
    }

    /**
     * Check whether given package is disabled
     *
     * @param $id
     * @return bool
     */
    public function disabled($id): bool
    {
        return !$this->enabled($id);
    }

    /**
     * Get package ID by class name
     *
     * @return string
     */
    public function getPackageIdByClass($class): string
    {
        // Str::kebab('Dice3D') = 'dice3-d', so need to add extra preg_replace()
        return preg_replace('#([0-9]+)-#', '-$1', Str::kebab(class_basename($class)));
    }

    public function getCodeVariable($id): string
    {
        return strtoupper(str_replace('-', '_', $id) . '_' . FP_CODE);
    }

    /**
     * Auto load necessary file when a package class is called
     *
     * @param $className
     */
    public function autoload($className)
    {
        foreach ($this->getInstalled() as $package) {
            // classes that are mapped one by one
            $static = (array) $package->static;

            // if specific class (such as seed) should be loaded
            if (in_array($className, array_keys($static))) {
                include_once base_path('packages/' . $package->base_path . '/' . $static[$className] . '/' . $className . '.php');
            // otherwise try to match by namespace
            } elseif (strpos($className, $package->namespace) !== FALSE) {
                include_once base_path('packages/' . $package->base_path . '/' . $package->source_path . '/' . str_replace([$package->namespace, '\\'], ['','/'], $className) . '.php');
            }
        }
    }
}
