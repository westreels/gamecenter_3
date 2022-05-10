<?php

namespace App\Services;

use App\Helpers\PackageManager;

class ActiveGameService
{

    /**
     * Create a new user account
     */
    public function run()
    {
        $games = [];
        foreach ((new PackageManager)->getEnabled() as $package) {
            $packageConfig = [];
            if ($package->type == 'game') {

                // provide only public variables if they are specified in the config file
                if ($publicVariables = config($package->id . '.public_variables')) {
                    collect($publicVariables)->each(function ($key) use ($package, &$packageConfig) {
                        // set a missing value within a nested array or object using "dot" notation:
                        data_fill($packageConfig, $key, config($package->id . '.' . $key));
                    });
                } else {
                    $packageConfig = config($package->id);
                }


                // add translations for game titles
                if (isset($packageConfig['variations'])) {
                    collect($packageConfig['variations'])->each(function ($variation) {
                        $variation->_title = __($variation->title);
                    });
                }
                $packageConfig['id'] = $package->id;
                $packageConfig['name'] = $package->name;
                $packageConfig['description'] = $package->description;

                $games[$package->id] = $packageConfig;
            }
        }

        $domainApp = config('app.url');
        $result = [];

        foreach ($games as $game) {
            if (isset($game['variations'])) {
                foreach ($game['variations'] as $itemGame) {
                    $gameImage = "{$domainApp}{$itemGame->banner}";
                    $urlGame = "{$domainApp}/games/{$game['id']}/$itemGame->slug";
                    $result[] = [
                        'id' => $itemGame->slug,
                        'name' => $itemGame->title,
                        'image' => $gameImage,
                        'description' => $game['description'],
                        'url' => "{$urlGame}?auto_login=1&uri={$urlGame}",
                    ];
                }
            } else {
                $gameImage = "{$domainApp}{$game['banner']}";
                $urlGame = "{$domainApp}/games/{$game['id']}";
                $result[] = [
                    'id' => $game['id'],
                    'name' => $game['name'],
                    'image' => $gameImage,
                    'description' => $game['description'],
                    'url' => "{$urlGame}?auto_login=1&uri={$urlGame}",
                ];
            }

        }

        return $result;
    }
}
