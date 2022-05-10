<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

//    'mailgun' => [
//        'domain' => env('MAILGUN_DOMAIN'),
//        'secret' => env('MAILGUN_SECRET'),
//        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
//    ],
//
//    'postmark' => [
//        'token' => env('POSTMARK_TOKEN'),
//    ],
//
//    'ses' => [
//        'key' => env('AWS_ACCESS_KEY_ID'),
//        'secret' => env('AWS_SECRET_ACCESS_KEY'),
//        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
//    ],
//
//    'sparkpost' => [
//        'secret' => env('SPARKPOST_SECRET'),
//    ],

    /*
     * Google Tag Manager
     */
    'gtm' => [
        'container_id' => env('GTM_CONTAINER_ID'),
    ],

    /*
     * Google reCaptcha
     */
    'recaptcha' => [
        'public_key' => env('RECAPTCHA_PUBLIC_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    ],

    'api' => [
        'crypto' => [
            'provider' => env('API_CRYPTO_PROVIDER', 'coincap'),
            'providers' => [
                'coincap' => [
                    'endpoint' => env('API_CRYPTO_PROVIDERS_COINCAP_ENDPOINT','https://api.coincap.io/v2/')
                ],
                'cryptocompare' => [
                    'endpoint' => env('API_CRYPTO_PROVIDERS_CRYPTOCOMPARE_ENDPOINT','https://min-api.cryptocompare.com/data/'),
                    'api_key' => env('API_CRYPTO_PROVIDERS_CRYPTOCOMPARE_API_KEY'),
                ]
            ]
        ],
    ],
];
