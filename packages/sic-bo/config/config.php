<?php

use Packages\SicBo\Helpers\SicBoBet;

return [
    'version'               => '1.0.0',
    'categories'            => json_decode(env('GAME_SIC_BO_CATEGORIES', json_encode(['Table']))),
    'banner'                => env('GAME_SIC_BO_BANNER', '/images/games/sic-bo.jpg'),
    'background'            => env('GAME_SIC_BO_BACKGROUND', ''),
    'min_bet'               => env('GAME_SIC_BO_MIN_BET', 1),
    'max_bet'               => env('GAME_SIC_BO_MAX_BET', 50),
    'bet_change_amount'     => env('GAME_SIC_BO_BET_CHANGE_AMOUNT', 1),
    'default_bet_amount'    => env('GAME_SIC_BO_DEFAULT_BET_AMOUNT', 1), // how much in credits to bet
    'paytable'              => json_decode(env('GAME_SIC_BO_PAYTABLE', json_encode([
        SicBoBet::BET_SMALL         => 2,
        SicBoBet::BET_BIG           => 2,
        SicBoBet::BET_SINGLE        => [2, 3, 4],
        SicBoBet::BET_DOUBLE        => 11,
        SicBoBet::BET_TRIPLE        => 181,
        SicBoBet::BET_ANY_TRIPLE    => 31,
        SicBoBet::BET_COMBINATION   => 7,
        SicBoBet::BET_TOTAL         => [7, 8, 9, 13, 19, 31, 61],
    ]))),
];
