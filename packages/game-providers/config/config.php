<?php

return [
    'version' => '1.0.0',

    'public_variables' => ['version'],

    'providers' => [
        'bgaming' => [
            'name' => env('GAME_PROVIDERS_BGAMING_NAME', 'BGaming'),
            'banners' => [
                'dark' => env('GAME_PROVIDERS_BGAMING_BANNER', '/images/game-providers/bgaming/banner-dark.png'),
                'light' => env('GAME_PROVIDERS_BGAMING_BANNER', '/images/game-providers/bgaming/banner-light.png'),
            ],
            'api' => [
                'id'            => env('GAME_PROVIDERS_BGAMING_API_ID'), // CASINO_ID
                'secret_key'    => env('GAME_PROVIDERS_BGAMING_API_SECRET_KEY'), // AUTH_TOKEN
                'endpoint'      => env('GAME_PROVIDERS_BGAMING_API_ENDPOINT', 'https://dev.bgaming-system.com'),
            ],
            'currency' => [
                'code' => env('GAME_PROVIDERS_BGAMING_CURRENCY_CODE', 'USD'),
                'decimals' => env('GAME_PROVIDERS_BGAMING_CURRENCY_DECIMALS', 2),
                'rate' => env('GAME_PROVIDERS_BGAMING_CURRENCY_RATE', 100),
            ],
        ],

        'evoplay' => [
            'name' => env('GAME_PROVIDERS_EVOPLAY_NAME', 'Evoplay'),
            'banners' => [
                'dark' => env('GAME_PROVIDERS_EVOPLAY_BANNER', '/images/game-providers/evoplay/banner-dark.png'),
                'light' => env('GAME_PROVIDERS_EVOPLAY_BANNER', '/images/game-providers/evoplay/banner-light.png'),
            ],
            'api' => [
                'id'                    => env('GAME_PROVIDERS_EVOPLAY_API_ID'), // project ID
                'secret_key'            => env('GAME_PROVIDERS_EVOPLAY_API_SECRET_KEY'),
                'endpoint'              => env('GAME_PROVIDERS_EVOPLAY_API_ENDPOINT', 'https://api.evoplay.games'),
                'version'               => env('GAME_PROVIDERS_EVOPLAY_API_VERSION', 1),
                'callback_version'      => env('GAME_PROVIDERS_EVOPLAY_API_CALLBACK_VERSION', 2),
                'allowed_callback_ips'  => json_decode(env('GAME_PROVIDERS_EVOPLAY_API_ALLOWED_CALLBACK_IPS', json_encode([
                    '172.255.228.12',
                    '172.255.228.228',
                    '172.255.228.4',
                    '82.115.221.121',
                    '82.115.221.122',
                    '82.115.221.123',
                    '82.115.221.124',
                    '82.115.221.125',
                    '82.115.221.126',
                    '119.42.60.212',
                    '119.42.60.220',
                    '119.42.60.52',
                    '119.42.60.11'
                ]))),
            ],
            'currency' => [
                'code' => env('GAME_PROVIDERS_EVOPLAY_CURRENCY_CODE', 'USD'),
                'decimals' => env('GAME_PROVIDERS_EVOPLAY_CURRENCY_DECIMALS', 2),
                'rate' => env('GAME_PROVIDERS_EVOPLAY_CURRENCY_RATE', 100),
            ],
        ],
    ],
];
