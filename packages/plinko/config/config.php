<?php

return [
    'version'               => '1.0.0',
    'categories'            => json_decode(env('GAME_PLINKO_CATEGORIES', json_encode(['Table']))),
    'banner'                => env('GAME_PLINKO_BANNER', '/images/games/plinko.jpg'),
    'background'            => env('GAME_PLINKO_BACKGROUND', ''),
    'min_bet'               => env('GAME_PLINKO_MIN_BET', 1),
    'max_bet'               => env('GAME_PLINKO_MAX_BET', 50),
    'bet_change_amount'     => env('GAME_PLINKO_BET_CHANGE_AMOUNT', 1),
    'default_bet_amount'    => env('GAME_PLINKO_DEFAULT_BET_AMOUNT', 1), // how much in credits to bet
    'paytable'              => json_decode(env('GAME_PLINKO_PAYTABLE', json_encode([
        25,
        5,
        2,
        1.5,
        0.5,
        0.25,
        0.5,
        1.5,
        2,
        5,
        25
    ]))),
];
