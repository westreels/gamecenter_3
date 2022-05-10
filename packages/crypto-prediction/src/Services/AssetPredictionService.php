<?php

namespace Packages\CryptoPrediction\Services;

use App\Models\Asset;
use App\Models\User;
use App\Services\AssetPredictionService as ParentAssetPredictionService;
use Packages\CryptoPrediction\Models\CryptoPrediction;

class AssetPredictionService extends ParentAssetPredictionService
{
    protected $gameableClass = CryptoPrediction::class;

    /**
     * Create a random game
     *
     * @param User $user
     */
    public static function createRandomGame(User $user): void
    {
        $instance = app()->makeWith(AssetPredictionService::class, ['user' => $user]);

        $minBet = intval(config('settings.bots.games.min_bet'));
        $maxBet = intval(config('settings.bots.games.max_bet'));

        $params = [
            'bet'       => random_int($minBet ?: config('crypto-prediction.min_bet'), $maxBet ?: config('crypto-prediction.max_bet')),
            'direction' => collect([-1, 1])->random(),
            'duration'  => collect(config('crypto-prediction.durations'))->random()->value
        ];

        $instance->make(
            Asset::active()->crypto()->inRandomOrder()->first(),
            $params
        );
    }
}
