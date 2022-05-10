<?php

return [
    'version' => '1.10.0',

    'public_variables' => [
        'deposit_min',
        'deposit_max',
        'withdrawal_min',
        'withdrawal_max',
        'stripe.public_key',
        'tron.network',
    ],

    'deposit_min'               => env('PAYMENTS_DEPOSIT_MIN', 100),
    'deposit_max'               => env('PAYMENTS_DEPOSIT_MAX', 999999999),
    'withdrawal_min'            => env('PAYMENTS_WITHDRAWAL_MIN', 100),
    'withdrawal_max'            => env('PAYMENTS_WITHDRAWAL_MAX', 999999999),
    'withdrawal_daily_limit'    => env('PAYMENTS_WITHDRAWAL_DAILY_LIMIT', 500),
    'withdrawal_weekly_limit'   => env('PAYMENTS_WITHDRAWAL_WEEKLY_LIMIT', 1000),
    'withdrawal_monthly_limit'  => env('PAYMENTS_WITHDRAWAL_MONTHLY_LIMIT', 5000),
    'withdrawal_auto_max'       => env('PAYMENTS_WITHDRAWAL_AUTO_MAX', 0),
    'withdrawal_only_profits'   => env('PAYMENTS_WITHDRAWAL_ONLY_PROFITS', TRUE),
    'min_total_deposit_to_withdraw' => env('PAYMENTS_MIN_TOTAL_DEPOSIT_TO_WITHDRAW', 0),

    'notifications' => [
        'admin' => [
            'deposit' => [
                'enabled' => env('PAYMENTS_NOTIFICATIONS_ADMIN_DEPOSIT_ENABLED', FALSE),
                'treshold' => env('PAYMENTS_NOTIFICATIONS_ADMIN_DEPOSIT_TRESHOLD', 0),
            ],
            'withdrawal' => [
                'enabled' => env('PAYMENTS_NOTIFICATIONS_ADMIN_WITHDRAWAL_ENABLED', FALSE),
                'treshold' => env('PAYMENTS_NOTIFICATIONS_ADMIN_WITHDRAWAL_TRESHOLD', 0),
            ],
        ],
    ],

    'paypal' => [
        'test_mode'     => env('PAYMENTS_PAYPAL_TEST_MODE', FALSE),
        'user'          => env('PAYMENTS_PAYPAL_USER'),
        'password'      => env('PAYMENTS_PAYPAL_PASSWORD'),
        'signature'     => env('PAYMENTS_PAYPAL_SIGNATURE'),
    ],

    'stripe' => [
        'public_key'        => env('PAYMENTS_STRIPE_PUBLIC_KEY'),
        'secret_key'        => env('PAYMENTS_STRIPE_SECRET_KEY'),
    ],

    'coinpayments' => [
        'merchant_id'               => env('PAYMENTS_COINPAYMENTS_MERCHANT_ID'),
        'public_key'                => env('PAYMENTS_COINPAYMENTS_PUBLIC_KEY'),
        'private_key'               => env('PAYMENTS_COINPAYMENTS_PRIVATE_KEY'),
        'secret_key'                => env('PAYMENTS_COINPAYMENTS_SECRET_KEY'),
        'withdrawals_auto_confirm'  => env('PAYMENTS_COINPAYMENTS_AUTO_CONFIRM_WITHDRAWALS', FALSE),
    ],

    'ethereum' => [
        'api_key' => env('PAYMENTS_ETHEREUM_ETHERSCAN_API_KEY'),
        'network' => env('PAYMENTS_ETHEREUM_NETWORK', 'main'),
    ],

    'bsc' => [
        'api_key' => env('PAYMENTS_BSC_EXPLORER_API_KEY'),
        'network' => env('PAYMENTS_BSC_NETWORK', 'bsc-main'), // bsc-main, bsc-test
    ],

    'polygon' => [
        'api_key' => env('PAYMENTS_POLYGON_EXPLORER_API_KEY'),
        'network' => env('PAYMENTS_POLYGON_NETWORK', 'polygon-main'), // polygon-main, polygon-test
    ],

    'tron' => [
        'network' => env('PAYMENTS_TRON_NETWORK', 'api.trongrid.io'),
    ],
];
