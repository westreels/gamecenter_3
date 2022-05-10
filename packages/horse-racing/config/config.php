<?php

use Packages\HorseRacing\Helpers\RaceBet;

return [
    'version'               => '1.0.1',
    'categories'            => json_decode(env('GAME_HORSE_RACING_CATEGORIES', json_encode(['Sport']))),
    'banner'                => env('GAME_HORSE_RACING_BANNER', '/images/games/horse-racing.jpg'),
    'background'            => env('GAME_HORSE_RACING_BACKGROUND', ''),
    'min_bet'               => env('GAME_HORSE_RACING_MIN_BET', 0),
    'max_bet'               => env('GAME_HORSE_RACING_MAX_BET', 50),
    'bet_change_amount'     => env('GAME_HORSE_RACING_BET_CHANGE_AMOUNT', 1),
    'runners'               => json_decode(env('GAME_HORSE_RACING_RUNNERS', json_encode([
        ['name' => 'Blazer', 'colors' => ['horse' => ['body' => '#BDB8B3', 'tail' => '#7A7373', 'pad' => ['text' => '#ffffff', 'background' => '#F2711C']],'jockey' => ['face' => '#FFDCB1', 'hat' => '#F2711C', 'shirt' => '#E6E0DD']]],
        ['name' => 'Wrangler', 'colors' => ['horse' => ['body' => '#4a2a04', 'tail' => '#000000', 'pad' => ['text' => '#ffffff', 'background' => '#0E9E3E']],'jockey' => ['face' => '#FFDCB1', 'hat' => '#000000', 'shirt' => '#0E9E3E']]],
        ['name' => 'Sheriff', 'colors' => ['horse' => ['body' => '#2E1901', 'tail' => '#000000', 'pad' => ['text' => '#012757', 'background' => '#C0D5E6']],'jockey' => ['face' => '#FFDCB1', 'hat' => '#012757', 'shirt' => '#012757']]],
        ['name' => 'Kentucky', 'colors' => ['horse' => ['body' => '#3B3936', 'tail' => '#3F3D39', 'pad' => ['text' => '#ffffff', 'background' => '#767676']],'jockey' => ['face' => '#FFDCB1', 'hat' => '#EBB905', 'shirt' => '#EBB905']]],
        ['name' => 'Diesel', 'colors' => ['horse' => ['body' => '#000000', 'tail' => '#000000', 'pad' => ['text' => '#ffffff', 'background' => '#DB2828']],'jockey' => ['face' => '#FFDCB1', 'hat' => '#DB2828', 'shirt' => '#F75858']]],
        ['name' => 'Tennessee', 'colors' => ['horse' => ['body' => '#FAFAF9', 'tail' => '#918F8F', 'pad' => ['text' => '#000000', 'background' => '#FFFFFF']],'jockey' => ['face' => '#FFDCB1', 'hat' => '#000000', 'shirt' => '#000000']]],
        ['name' => 'Ladybird', 'colors' => ['horse' => ['body' => '#532F11', 'tail' => '#613B1C', 'pad' => ['text' => '#ffffff', 'background' => '#532F11']],'jockey' => ['face' => '#FFDCB1', 'hat' => '#532F11', 'shirt' => '#B3A091']]],
        ['name' => 'Sassy', 'colors' => ['horse' => ['body' => '#AA7719', 'tail' => '#5F3F03', 'pad' => ['text' => '#6E0491', 'background' => '#DFC5E7']],'jockey' => ['face' => '#FFDCB1', 'hat' => '#6E0491', 'shirt' => '#6E0491']]]
    ]))),
    'paytable'              => json_decode(env('GAME_HORSE_RACING_PAYTABLE', json_encode([
        RaceBet::BET_WIN    => [1.9, 2, 2.1, 2, 2.2, 2.4, 2.05, 2.3], // horses 1-8
        RaceBet::BET_PLACE  => [1.8, 1.5, 1.45, 1.65, 1.35, 1.5, 1.55, 1.6], // horses 1-8
        RaceBet::BET_SHOW   => [1.05, 1.1, 1.15, 1.1, 1.2, 1.12, 1.1, 1.18], // horses 1-8
    ]))),
    'animation' => [
        'length' => env('GAME_HORSE_RACING_ANIMATION_LENGTH', 15),
        'overtake_multiplier' => env('GAME_HORSE_RACING_ANIMATION_OVERTAKE_MULTIPLIER', 0.01)
    ]
];
