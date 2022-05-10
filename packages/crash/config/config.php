<?php

use Packages\Crash\Models\Crash;

return [
    'version'               => '1.0.0',

    'public_variables' => [
        'categories',
        'banner',
        'background',
        'min_bet',
        'max_bet',
        'bet_change_amount',
        'default_bet_amount',
        'duration',
        'base_number',
        'animation',
    ],

    'categories'            => json_decode(env('GAME_CRASH_CATEGORIES', json_encode(['Multiplayer']))),
    'banner'                => env('GAME_CRASH_BANNER', '/images/games/crash.jpg'),
    'background'            => env('GAME_CRASH_BACKGROUND', ''),
    'min_bet'               => env('GAME_CRASH_MIN_BET', 1),
    'max_bet'               => env('GAME_CRASH_MAX_BET', 50),
    'bet_change_amount'     => env('GAME_CRASH_BET_CHANGE_AMOUNT', 1),
    'default_bet_amount'    => env('GAME_CRASH_DEFAULT_BET_AMOUNT', 1),
    'interval'              => env('GAME_CRASH_INTERVAL', 5), // delay before next betting round starts (in seconds)
    'duration'              => env('GAME_CRASH_DURATION', 15), // betting round duration (in seconds)
    'payout_intervals'      => json_decode(env('GAME_CRASH_PAYOUT_INTERVALS', json_encode([1.1, 1.3, 1.5, 1.7, 2, 2.5, 10]))),
    'base_number'           => env('GAME_CRASH_BASE_NUMBER', 1.05), // base number in the exponential function n^x
    'allowed_end_time_lag'  => env('GAME_CRASH_ALLOWED_END_TIME_LAG', 1000), // in milliseconds
    'bots_enabled'          => env('GAME_CRASH_BOTS_ENABLED', TRUE),
    'animation'             => json_decode(env('GAME_CRASH_ANIMATION', json_encode([
        'time_limit'            => 12, // time in seconds for the rocket to reach the planet
        'airplanes_count'       => 3,
        'clouds_count'          => 5
    ]))),
];
