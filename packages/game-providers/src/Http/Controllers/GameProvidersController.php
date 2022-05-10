<?php

namespace Packages\GameProviders\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Packages\GameProviders\Helpers\Api;

class GameProvidersController extends Controller
{
    public function getProviders()
    {
        $providers = collect(config('game-providers.providers'))
            ->filter(function ($provider, $providerId) {
                return Api::make($providerId)->isEnabled();
            })
            ->map(function ($provider) {
                return [
                    'name' => $provider['name'],
                    'banner' => $provider['banners'][config('settings.theme.mode')],
                ];
            });

        return $providers;
    }

    public function getAllGames()
    {
        $games = collect();

        collect(config('game-providers.providers'))
            ->keys()
            ->each(function ($provider) use (&$games) {
                $api = Api::make($provider);
                if ($api->isEnabled()) {
                    $games = $games->concat($api->getGamesList()->values());
                }
            });

        return $games;
    }

    public function getProviderGames(Request $request, string $provider)
    {
        $api = NULL;

        try {
            $api = Api::make($provider);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        if (!$api || !$api->isEnabled()) {
            abort(404);
        }

        return $api->getGamesList()->values();
    }
}
