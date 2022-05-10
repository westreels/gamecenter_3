<?php

return [
    'version'               => '1.1.0',
    'categories'            => json_decode(env('GAME_BACCARAT_CATEGORIES', json_encode(['Cards']))),
    'banner'                => env('GAME_BACCARAT_BANNER', '/images/games/baccarat.jpg'),
    'background'            => env('GAME_BACCARAT_BACKGROUND', ''),
    'min_bet'               => env('GAME_BACCARAT_MIN_BET', 1),
    'max_bet'               => env('GAME_BACCARAT_MAX_BET', 50),
    'bet_change_amount'     => env('GAME_BACCARAT_BET_CHANGE_AMOUNT', 1),
    'default_bet_amount'    => env('GAME_BACCARAT_DEFAULT_BET_AMOUNT', 1), // how much in credits to bet
    'payouts' => [
        'player'    => env('GAME_BACCARAT_PAYOUT_PLAYER', 2),       // 1:1
        'tie'       => env('GAME_BACCARAT_PAYOUT_TIE', 9),          // 8:1
        'banker'    => env('GAME_BACCARAT_PAYOUT_BANKER', 1.95),    // 1:1 (with 5% commission)
    ]
];
