<?php

return [
    'version'    => '1.1.0',
    'variations' => json_decode(env('GAME_SLOTS_VARIATIONS', json_encode([
        [
            'id'                    => 0,
            'title'                 => 'Fruit slots',
            'slug'                  => 'fruits',
            'min_bet'               => 1,
            'max_bet'               => 50,
            'bet_change_amount'     => 1,
            'default_bet_amount'    => 1,
            'default_lines'         => 20,
            'categories'            => ['Slots'],
            'banner'                => '/images/games/slots/0/banner.jpg',
            'background'            => '/images/games/slots/0/background.jpg',
            'symbols' => [
                ['image' => '/images/games/slots/0/apple.png',         'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,1,2,5,10]],
                ['image' => '/images/games/slots/0/cherry.png',        'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,0,5,10,15]],
                ['image' => '/images/games/slots/0/lemon.png',         'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,1,2,5,10]],
                ['image' => '/images/games/slots/0/orange.png',        'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,1,2,5,10]],
                ['image' => '/images/games/slots/0/pineapple.png',     'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,0,3,9,12]],
                ['image' => '/images/games/slots/0/plum.png',          'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,1,2,5,10]],
                ['image' => '/images/games/slots/0/star.png',          'wild' => TRUE,  'scatter' => FALSE, 'payouts' => [0,0,0,0,0]],
                ['image' => '/images/games/slots/0/strawberry.png',    'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,1,2,5,10]],
                ['image' => '/images/games/slots/0/watermelon.png',    'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,1,2,5,10]],
                ['image' => '/images/games/slots/0/seven.png',         'wild' => FALSE, 'scatter' => TRUE,  'payouts' => [0,0,10,20,50]],
            ],
            'reels' => [
                [0,1,2,3,4,5,6,7,8,9],
                [1,2,3,4,5,6,7,8,9,0],
                [2,3,4,5,6,7,8,9,0,1],
                [3,4,5,6,7,8,9,0,1,2],
                [4,5,6,7,8,9,0,1,2,3],
            ]
        ],
        [
            'id'                    => 1,
            'title'                 => 'Animal slots',
            'slug'                  => 'animals',
            'min_bet'               => 1,
            'max_bet'               => 50,
            'bet_change_amount'     => 1,
            'default_bet_amount'    => 1,
            'default_lines'         => 20,
            'categories'            => ['Slots'],
            'banner'                => '/images/games/slots/1/banner.jpg',
            'symbols' => [
                ['image' => '/images/games/slots/1/cat.png',       'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,1,2,5,10]],
                ['image' => '/images/games/slots/1/dog.png',       'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,1,5,10,15]],
                ['image' => '/images/games/slots/1/elephant.png',  'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,1,2,5,10]],
                ['image' => '/images/games/slots/1/flamingo.png',  'wild' => FALSE, 'scatter' => TRUE,  'payouts' => [0,0,10,20,30]],
                ['image' => '/images/games/slots/1/frog.png',      'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,1,3,9,12]],
                ['image' => '/images/games/slots/1/horse.png',     'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,1,2,5,10]],
                ['image' => '/images/games/slots/1/monkey.png',    'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,1,2,4,10]],
                ['image' => '/images/games/slots/1/penguin.png',   'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,1,2,5,10]],
                ['image' => '/images/games/slots/1/tiger.png',     'wild' => FALSE, 'scatter' => FALSE, 'payouts' => [0,1,2,5,10]]
            ],
            'reels' => [
                [0,1,2,3,4,5,6,7,8],
                [1,2,3,4,5,6,7,8,0],
                [2,3,4,5,6,7,8,0,1],
                [3,4,5,6,7,8,0,1,2],
                [4,5,6,7,8,0,1,2,3],
            ]
        ]
    ])))
];
