<?php

return [
    'version'               => '1.0.5',
    'categories'            => json_decode(env('GAME_VIDEO_POKER_CATEGORIES', json_encode(['Cards','Poker']))),
    'banner'                => env('GAME_VIDEO_POKER_BANNER', '/images/games/video-poker.jpg'),
    'background'            => env('GAME_VIDEO_POKER_BACKGROUND', ''),
    'min_bet'               => env('GAME_VIDEO_POKER_MIN_BET', 1),
    'max_bet'               => env('GAME_VIDEO_POKER_MAX_BET', 50),
    'bet_change_amount'     => env('GAME_VIDEO_POKER_BET_CHANGE_AMOUNT', 1),
    'default_bet_amount'    => env('GAME_VIDEO_POKER_DEFAULT_BET_AMOUNT', 1), // how much in credits to bet
    'paytable'              => json_decode(env('GAME_VIDEO_POKER_PAYTABLE', json_encode([0,1,2,3,4,5,10,20,40,200])))
];
