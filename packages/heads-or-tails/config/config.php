<?php

return [
    'version'               => '1.0.3',
    'categories'            => json_decode(env('GAME_HEADS_OR_TAILS_CATEGORIES', json_encode(['Table']))),
    'banner'                => env('GAME_HEADS_OR_TAILS_BANNER', '/images/games/heads-or-tails.jpg'),
    'background'            => env('GAME_HEADS_OR_TAILS_BACKGROUND', '/images/games/heads-or-tails/background.jpg'),
    'min_bet'               => env('GAME_HEADS_OR_TAILS_MIN_BET', 1),
    'max_bet'               => env('GAME_HEADS_OR_TAILS_MAX_BET', 50),
    'bet_change_amount'     => env('GAME_HEADS_OR_TAILS_BET_CHANGE_AMOUNT', 1),
    'default_bet_amount'    => env('GAME_HEADS_OR_TAILS_DEFAULT_BET_AMOUNT', 1), // how much in credits to bet
    'house_edge'            => env('GAME_HEADS_OR_TAILS_HOUSE_EDGE', 5), // house edge in %
    'images' => [
        'heads' => env('GAME_HEADS_OR_TAILS_IMAGES_HEADS', '/images/games/heads-or-tails/heads.jpg'),
        'tails' => env('GAME_HEADS_OR_TAILS_IMAGES_TAILS', '/images/games/heads-or-tails/tails.jpg'),
        'edge'  => env('GAME_HEADS_OR_TAILS_IMAGES_EDGE', '/images/games/heads-or-tails/edge.jpg'),
    ],
    'animation' => [
        'camera_angle' => env('GAME_HEADS_OR_TAILS_ANIMATION_CAMERA_ANGLE', 15),
        'camera_position' => env('GAME_HEADS_OR_TAILS_ANIMATION_CAMERA_POSITION', -20),
        'throw_height' => env('GAME_HEADS_OR_TAILS_ANIMATION_THROW_HEIGHT', 67),
    ]
];
