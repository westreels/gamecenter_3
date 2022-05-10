<?php

return [
    'version'               => '1.1.0',
    'categories'            => json_decode(env('GAME_DICE_CATEGORIES', json_encode(['Table']))),
    'banner'                => env('GAME_DICE_BANNER', '/images/games/dice.jpg'),
    'background'            => env('GAME_DICE_BACKGROUND', ''),
    'min_bet'               => env('GAME_DICE_MIN_BET', 1),
    'max_bet'               => env('GAME_DICE_MAX_BET', 50),
    'bet_change_amount'     => env('GAME_DICE_BET_CHANGE_AMOUNT', 1),
    'default_bet_amount'    => env('GAME_DICE_DEFAULT_BET_AMOUNT', 1), // how much in credits to bet
    'min_win_chance'        => env('GAME_DICE_MIN_WIN_CHANCE', 2),
    'max_win_chance'        => env('GAME_DICE_MAX_WIN_CHANCE', 98),
    'house_edge'            => env('GAME_DICE_HOUSE_EDGE', 2), // house edge in %
    'auto_play_enabled'     => env('GAME_DICE_AUTO_PLAY_ENABLED', TRUE),
];
