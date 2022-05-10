<?php

return [
    'version'               => '1.0.3',
    'categories'            => json_decode(env('GAME_CASINO_HOLDEM_CATEGORIES', json_encode(['Cards', 'Poker']))),
    'banner'                => env('GAME_CASINO_HOLDEM_BANNER', '/images/games/casino-holdem.jpg'),
    'background'            => env('GAME_CASINO_HOLDEM_BACKGROUND', ''),
    // ante bet
    'min_bet'               => env('GAME_CASINO_HOLDEM_MIN_BET', 1),
    'max_bet'               => env('GAME_CASINO_HOLDEM_MAX_BET', 50),
    'bet_change_amount'     => env('GAME_CASINO_HOLDEM_BET_CHANGE_AMOUNT', 1),
    'default_bet_amount'    => env('GAME_CASINO_HOLDEM_DEFAULT_BET_AMOUNT', 1), // how much in credits to bet
    // AA bonus bet
    'min_bonus_bet'             => env('GAME_CASINO_HOLDEM_MIN_BONUS_BET', 0),
    'max_bonus_bet'             => env('GAME_CASINO_HOLDEM_MAX_BONUS_BET', 50),
    'bonus_bet_change_amount'   => env('GAME_CASINO_HOLDEM_BONUS_BET_CHANGE_AMOUNT', 1),
    'default_bonus_bet_amount'  => env('GAME_CASINO_HOLDEM_DEFAULT_BONUS_BET_AMOUNT', 0),

    'ante_paytable' => json_decode(env('GAME_CASINO_HOLDEM_ANTE_PAYTABLE', json_encode([
        0 => 0, // HAND_HIGH_CARD
        1 => 2, // HAND_PAIR, 1:1
        2 => 2, // HAND_TWO_PAIR, 1:1
        3 => 2, // HAND_THREE_OF_A_KIND, 1:1
        4 => 2, // HAND_STRAIGHT, 1:1
        5 => 3, // HAND_FLUSH, 2:1
        6 => 4, // HAND_FULL_HOUSE, 3:1
        7 => 11, // HAND_FOUR_OF_A_KIND, 10:1
        8 => 21, // HAND_STRAIGHT_FLUSH, 20:1
        9 => 101, // HAND_ROYAL_FLUSH, 100:1
    ]))),

    'bonus_paytable' => json_decode(env('GAME_CASINO_HOLDEM_BONUS_PAYTABLE', json_encode([
        0 => 0, // HAND_HIGH_CARD
        1 => 8, // HAND_PAIR, [ONLY 2 ACES], 7:1 (win equals bet * 8)
        2 => 8, // HAND_TWO_PAIR, 7:1
        3 => 8, // HAND_THREE_OF_A_KIND, 7:1
        4 => 8, // HAND_STRAIGHT, 1:1
        5 => 21, // HAND_FLUSH, 20:1
        6 => 31, // HAND_FULL_HOUSE, 30:1
        7 => 41, // HAND_FOUR_OF_A_KIND, 40:1
        8 => 51, // HAND_STRAIGHT_FLUSH, 50:1
        9 => 101, // HAND_ROYAL_FLUSH, 100:1
    ]))),
];
