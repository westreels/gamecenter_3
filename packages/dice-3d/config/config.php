<?php

return [
    'version'               => '1.0.6',
    'categories'            => json_decode(env('GAME_DICE_3D_CATEGORIES', json_encode(['Table']))),
    'banner'                => env('GAME_DICE_3D_BANNER', '/images/games/dice-3d.jpg'),
    'background'            => env('GAME_DICE_3D_BACKGROUND', ''),
    'min_bet'               => env('GAME_DICE_3D_MIN_BET', 1),
    'max_bet'               => env('GAME_DICE_3D_MAX_BET', 50),
    'bet_change_amount'     => env('GAME_DICE_3D_BET_CHANGE_AMOUNT', 1),
    'default_bet_amount'    => env('GAME_DICE_3D_DEFAULT_BET_AMOUNT', 1), // how much in credits to bet
    'house_edge'            => env('GAME_DICE_3D_HOUSE_EDGE', 2), // house edge in %
    'fill_color'            => env('GAME_DICE_3D_FILL_COLOR', '#1976D2'),
    'font_color'            => env('GAME_DICE_3D_FONT_COLOR', '#FFFFFF'),
    'dice_types'            => [
        'tetrahedron' => [
            'min' => 1,
            'max' => 4
        ],
        'cube' => [
            'min' => 1,
            'max' => 6
        ],
        'octahedron' => [
            'min' => 1,
            'max' => 8
        ],
        'dipyramid' => [
            'min' => 1,
            'max' => 10
        ],
        'dodecahedron' => [
            'min' => 1,
            'max' => 12
        ],
        'icosahedron' => [
            'min' => 1,
            'max' => 20
        ],
    ],
    'dice' => json_decode(env('GAME_DICE_3D_DICE', json_encode([
        'tetrahedron',
        'cube',
        'octahedron',
        'dipyramid',
        'dodecahedron',
        'icosahedron'
    ]))),
];
