<?php

return [
    'version'               => '1.0.0',
    'categories'            => json_decode(env('GAME_KENO_CATEGORIES', json_encode(['Lottery']))),
    'banner'                => env('GAME_KENO_BANNER', '/images/games/keno.jpg'),
    'background'            => env('GAME_KENO_BACKGROUND', ''),
    'min_bet'               => env('GAME_KENO_MIN_BET', 1),
    'max_bet'               => env('GAME_KENO_MAX_BET', 50),
    'bet_change_amount'     => env('GAME_KENO_BET_CHANGE_AMOUNT', 1),
    'default_bet_amount'    => env('GAME_KENO_DEFAULT_BET_AMOUNT', 1), // how much in credits to bet
    'rows_count'            => env('GAME_KENO_ROWS_COUNT', 8),
    'cols_count'            => env('GAME_KENO_COLS_COUNT', 10),
    'select_count'          => env('GAME_KENO_SELECT_COUNT', 10), // how many numbers a player can select
    'draw_count'            => env('GAME_KENO_DRAW_COUNT', 20), // how many numbers to draw
    'paytable'              => json_decode(env('GAME_KENO_PAYTABLE', json_encode([
        0,
        0,
        0,
        1,
        5,
        25,
        125,
        250,
        500,
        1000
    ]))),
];
