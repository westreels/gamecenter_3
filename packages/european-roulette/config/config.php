<?php

return [
    'version'               => '1.0.9',
    'categories'            => json_decode(env('GAME_EUROPEAN_ROULETTE_CATEGORIES', json_encode(['Roulette']))),
    'banner'                => env('GAME_EUROPEAN_ROULETTE_BANNER', '/images/games/european-roulette.jpg'),
    'background'            => env('GAME_EUROPEAN_ROULETTE_BACKGROUND', ''),
    'min_bet'               => env('GAME_EUROPEAN_ROULETTE_MIN_BET', 1),
    'max_bet'               => env('GAME_EUROPEAN_ROULETTE_MAX_BET', 50),
    'bet_change_amount'     => env('GAME_EUROPEAN_ROULETTE_BET_CHANGE_AMOUNT', 1),
    'default_bet_amount'    => env('GAME_EUROPEAN_ROULETTE_DEFAULT_BET_AMOUNT', 1), // how much in credits to bet
];
