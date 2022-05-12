<?php

return [
    'social' => [
        'domain' => env('SOCIAL_DOMAIN', 'https://my.bizverse.world'),
        'server_key' => env('SOCIAL_SERVER_KEY', 'c16c4d96ae7eae09f9e9100902c478ec'),
        // 'app_id' => env('SOCIAL_APP_ID', '1a332b034f8372c513ca'),
        // 'app_secret' => env('SOCIAL_APP_SECRET', 'beedae6421773ad9a1377f212d8a4698a5b08bd'),
        'app_id' => env('SOCIAL_APP_ID', '0e0c42c6c478f1b9f925'),
        'app_secret' => env('SOCIAL_APP_SECRET', '1ff4800c8caad3e92b257dbff7cff28771cf0f2'),
    ],
    'api_balance' => [
        'domain' => env('API_BALANCE_DOMAIN', 'https://relaxzone-api.bizverse.world'),
    ],
    'balance_dev' => [
        'domain' => env('API_BALANCE_DOMAIN', 'https://vrfairs-dev.bizverse.world/api'),
        'HMAC_GAME' => env('dv2dyl4IcDGP1n3PhFpz8cju7tbTK1tY'),
    ],
    'address_wallet_total' => env('TOTAL_WALLET_ADDRESS', '0x52b2c332910D154Ac4973556590dfCea91704134'),
    'hash_secret_key' => env('HASH_SECRET_KEY', 'z16ynb1lps'),
];
