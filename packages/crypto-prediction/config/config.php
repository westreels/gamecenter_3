<?php

use Carbon\CarbonInterval;

$durations = [
    ['value' => 15, 'text' => CarbonInterval::seconds(15)->cascade()->forHumans()],
    ['value' => 30, 'text' => CarbonInterval::seconds(30)->cascade()->forHumans()],
    ['value' => 60, 'text' => CarbonInterval::seconds(60)->cascade()->forHumans()],
    ['value' => 120, 'text' => CarbonInterval::seconds(120)->cascade()->forHumans()],
    ['value' => 180, 'text' => CarbonInterval::seconds(180)->cascade()->forHumans()],
    ['value' => 300, 'text' => CarbonInterval::seconds(300)->cascade()->forHumans()],
    ['value' => 600, 'text' => CarbonInterval::seconds(600)->cascade()->forHumans()],
    ['value' => 900, 'text' => CarbonInterval::seconds(900)->cascade()->forHumans()],
    ['value' => 1800, 'text' => CarbonInterval::seconds(1800)->cascade()->forHumans()],
    ['value' => 2700, 'text' => CarbonInterval::seconds(2700)->cascade()->forHumans()],
    ['value' => 3600, 'text' => CarbonInterval::seconds(3600)->cascade()->forHumans()],
    ['value' => 7200, 'text' => CarbonInterval::seconds(7200)->cascade()->forHumans()],
    ['value' => 10800, 'text' => CarbonInterval::seconds(10800)->cascade()->forHumans()],
    ['value' => 21600, 'text' => CarbonInterval::seconds(21600)->cascade()->forHumans()],
    ['value' => 43200, 'text' => CarbonInterval::seconds(43200)->cascade()->forHumans()],
    ['value' => 86400, 'text' => CarbonInterval::seconds(86400)->cascade()->forHumans()],
    ['value' => 172800, 'text' => CarbonInterval::seconds(172800)->cascade()->forHumans()],
    ['value' => 432000, 'text' => CarbonInterval::seconds(432000)->cascade()->forHumans()],
    ['value' => 604800, 'text' => CarbonInterval::seconds(604800)->cascade()->forHumans()],
];

return [
    'version'               => '1.1.1',
    'banner'                => env('CRYPTO_PREDICTION_BANNER', '/images/games/crypto-prediction.jpg'),
    'background'            => env('CRYPTO_PREDICTION_BACKGROUND', ''),
    'min_bet'               => env('CRYPTO_PREDICTION_MIN_BET', 1),
    'max_bet'               => env('CRYPTO_PREDICTION_MAX_BET', 50),
    'bet_change_amount'     => env('CRYPTO_PREDICTION_BET_CHANGE_AMOUNT', 1),
    'default_bet_amount'    => env('CRYPTO_PREDICTION_DEFAULT_BET_AMOUNT', 1), // how much in credits to bet
    'default_asset_name'    => env('CRYPTO_PREDICTION_DEFAULT_ASSET_NAME', 'Bitcoin'),
    'asset_max_rank'        => env('CRYPTO_PREDICTION_ASSET_MAX_RANK', 100),
    'price_update_interval' => env('CRYPTO_PREDICTION_PRICE_UPDATE_INTERVAL', 5),
    'available_durations'   => $durations,
    'durations'             => json_decode(env('CRYPTO_PREDICTION_DURATIONS', json_encode($durations))),
    'paytable'              => json_decode(env('CRYPTO_PREDICTION_PAYTABLE', json_encode([
        -1  => 1.9, // sell
        1   => 1.9 // buy
    ])))
];
