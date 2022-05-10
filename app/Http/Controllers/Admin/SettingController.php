<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PackageManager;
use App\Http\Controllers\Controller;
use App\Services\DotEnvService;
use Database\Seeders\AssetSeeder;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    public function get(PackageManager $packageManager)
    {
        $config = collect(['app', 'broadcasting', 'database', 'logging', 'mail', 'oauth', 'queue', 'settings', 'services', 'session'])
            ->mapWithKeys(function ($item, $key) {
                // 'app' => config('app')
                return [$item => config($item)];
            });

        foreach ($packageManager->getEnabled() as $id => $package) {
            $config->put($id, config($id));
        }

        return response()->json(['config' => $config])->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

    public function update(Request $request, DotEnvService $dotEnvService)
    {
        // save settings to .env file
        return $dotEnvService->save($request->all())
            ? $this->successResponse(__('Settings successfully saved.'))
            : $this->errorResponse(__('There was an error while saving the settings.') . ' ' . __('Please check that .env file exists and has write permissions.'));
    }

    public function runAssetSeeder()
    {
        Artisan::call(SeedCommand::class, ['--class' => AssetSeeder::class, '--force' => TRUE]);

        return $this->successResponse();
    }
}
