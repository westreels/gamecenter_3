<?php

return [
    /*
     * Theme
     */
    'theme' => [
        'mode'  => env('THEME_MODE', 'dark'),
        'colors' => [
            'primary'   => env('THEME_COLOR_PRIMARY', '#1976D2'),
            'secondary' => env('THEME_COLOR_SECONDARY', '#424242'),
            'accent'    => env('THEME_COLOR_ACCENT', '#82B1FF'),
            'error'     => env('THEME_COLOR_ERROR', '#FF5252'),
            'info'      => env('THEME_COLOR_INFO', '#2196F3'),
            'success'   => env('THEME_COLOR_SUCCESS', '#4CAF50'),
            'warning'   => env('THEME_COLOR_WARNING', '#FFC107'),
            'anchor'    => env('THEME_COLOR_ANCHOR', '#1976D2'),
        ],
        'background' => env('THEME_BACKGROUND', 'Circles'),
    ],

    /*
     * Number formatting
     */
    'format' => [
        'number' => [
            'decimal_separator'     => env('FORMAT_NUMBER_DECIMAL_SEPARATOR', ord('.')),
            'thousands_separator'   => env('FORMAT_NUMBER_THOUSANDS_SEPARATOR', ord(',')),
        ]
    ],

    /*
     * Require users to verify their email
     */
    'email_verification' => env('EMAIL_VERIFICATION', FALSE),

    /*
     * Bonuses
     */
    'bonuses' => [
        'sign_up' => env('BONUSES_SIGN_UP', 100), // regular user sign up bonus
        'email_verification' => env('BONUSES_EMAIL_VERIFICATION', 50), // regular user sign up bonus
        'faucet' => [
            'amount' => env('BONUSES_FAUCET_AMOUNT', 100),
            'interval' => env('BONUSES_FAUCET_INTERVAL', 48),
        ],
        'deposit' => [
            'threshold' => env('BONUSES_DEPOSIT_THRESHOLD', 500),
            'pct'       => env('BONUSES_DEPOSIT_PCT', 5),
        ],
        'rain' => [
            'frequency' => env('BONUSES_RAIN_FREQUENCY', 'daily'),
            'user' => env('BONUSES_RAIN_USER', NULL), // user in chat who makes the rain
            'amounts' => json_decode(env('BONUSES_RAIN_AMOUNTS', json_encode([100, 50, 25]))),
        ]
    ],

    /*
     * Affiliate program (3 tiers)
     */
    'affiliate' => [
        'allow_same_ip' => env('AFFILIATE_ALLOW_SAME_IP', false),

        'auto_approval_frequency' => env('AFFILIATE_AUTO_APPROVAL_FREQUENCY', 'weekly'),

        'commissions' => [
            'sign_up'   => [
                'type'      => 'fixed',
                'rates'     => json_decode(env('AFFILIATE_COMMISSIONS_SIGN_UP', json_encode([100, 50, 25]))),
            ],
            'game_loss' => [
                'type'      => 'percentage',
                'rates'     => json_decode(env('AFFILIATE_COMMISSIONS_GAME_LOSS', json_encode([10, 5, 1]))),
            ],
            'game_win'  => [
                'type'      => 'percentage',
                'rates'     => json_decode(env('AFFILIATE_COMMISSIONS_GAME_WIN', json_encode([10, 5, 1]))),
            ],
            'deposit'  => [
                'type'      => 'percentage',
                'rates'     => json_decode(env('AFFILIATE_COMMISSIONS_DEPOSIT', json_encode([5, 3, 1]))),
            ],
        ],
    ],

    'notifications' => [
        'admin' => [
            'email' => env('NOTIFICATIONS_ADMIN_EMAIL', ''),
            'registration' => [
                'enabled' => env('NOTIFICATIONS_ADMIN_REGISTRATION_ENABLED', FALSE),
            ],
            'game' => [
                'win' => [
                    'enabled' => env('NOTIFICATIONS_ADMIN_GAME_WIN_ENABLED', FALSE),
                    'treshold' => env('NOTIFICATIONS_ADMIN_GAME_WIN_TRESHOLD', 1000),
                ],
                'loss' => [
                    'enabled' => env('NOTIFICATIONS_ADMIN_GAME_LOSS_ENABLED', FALSE),
                    'treshold' => env('NOTIFICATIONS_ADMIN_GAME_LOSS_TRESHOLD', 1000),
                ]
            ],
        ],
    ],

    /*
     * General games settings
     */
    'games' => [
        'playing_cards' => [
            'front_image' => env('GAMES_PLAYING_CARDS_FRONT_IMAGE', '/images/games/playing-cards/front.svg'),
            'back_image' => env('GAMES_PLAYING_CARDS_BACK_IMAGE', '/images/games/playing-cards/back.svg'),
            'deck' => env('GAMES_PLAYING_CARDS_DECK', 'poker'),
        ],
        'multiplayer' => [
            // max number of rooms one user can create and have open at the same time
            'rooms_creation_limit' => env('GAMES_MULTIPLAYER_ROOMS_CREATION_LIMIT', 2)
        ]
    ],

    /*
     * Bots configuration
     */
    'bots' => [
        'games' => [
            // should correspond to a valid scheduling method without parameters
            // https://laravel.com/docs/6.x/scheduling#schedule-frequency-options
            'frequency' => env('BOTS_GAMES_FREQUENCY', 'hourly'),
            'min_count' => env('BOTS_GAMES_MIN_COUNT', 1),
            'max_count' => env('BOTS_GAMES_MAX_COUNT', 10),
            'min_bet'   => env('BOTS_GAMES_MIN_BET'),
            'max_bet'   => env('BOTS_GAMES_MAX_BET'),
        ]
    ],

    'interface' => [
        'credits_icon' => env('INTERFACE_CREDITS_ICON', 'mdi-poker-chip'),
        'navbar' => [
            'visible' => env('INTERFACE_NAVBAR_VISIBLE', FALSE),
        ],
        'online' => [
            'enabled' => env('INTERFACE_ONLINE_ENABLED', FALSE),
        ],
        'system_bar' => [
            'enabled' => env('INTERFACE_SYSTEM_BAR_ENABLED', FALSE),
        ],
        'chat' => [
            'enabled' => env('CHAT_ENABLED', FALSE),
            'message_max_length' => env('CHAT_MESSAGE_MAX_LENGTH', 150)
        ],
        'game_feed' => [
            'delay' => env('INTERFACE_GAME_FEED_DELAY', 10),
            'timeout' => env('INTERFACE_GAME_FEED_TIMEOUT', 10)
        ]
    ],

    'content' => [
        'home' => [
            'slider' => json_decode(env('CONTENT_HOME_SLIDER', json_encode([
                'height' => 500,
                'navigation' => TRUE,
                'pagination' => FALSE,
                'cycle' => TRUE,
                'interval' => 5, // seconds
                'slides' => [
                    [
                        'title' => 'Bizverse Game Center',
                        'subtitle' => 'Play to Earn',
                        'image' => [
                            'url' => '/images/home/banner.jpg',
                        ],
                        'link' => [
                            'title' => '',
                            'url' => '',
                        ]
                    ],
                    [
                        'title' => 'Test your luck',
                        'subtitle' => 'Play our games and win big time!',
                        'image' => [
                            'url' => '/images/home/banner2.jpg',
                        ],
                        'link' => [
                            'title' => 'I want to try',
                            'url' => '/register',
                        ]
                    ]
                ]
            ]))),
            'games' => [
                'display_style' => env('CONTENT_HOME_GAMES_DISPLAY_STYLE', 'card'),
                'display_count' => env('CONTENT_HOME_GAMES_DISPLAY_COUNT', 12),
                'load_count' => env('CONTENT_HOME_GAMES_LOAD_COUNT', 6),
            ],
            'provider_games' => [
                'display_style' => env('CONTENT_HOME_PROVIDER_GAMES_DISPLAY_STYLE', 'block2'),
                'display_count' => env('CONTENT_HOME_PROVIDER_GAMES_DISPLAY_COUNT', 6),
                'load_count' => env('CONTENT_HOME_PROVIDER_GAMES_LOAD_COUNT', 6),
                'featured_categories' => json_decode(env('CONTENT_HOME_PROVIDER_GAMES_FEATURED_CATEGORIES', json_encode([
                    ['title' => 'Slots', 'categories' => ['Slot', 'Slots']],
                    ['title' => 'Roulette', 'categories' => ['Roulette']],
                    ['title' => 'Poker', 'categories' => ['Poker', 'Video poker']],
                    ['title' => 'Other card games', 'categories' => ['Card', 'Blackjack', 'Baccarat']],
                ]))),
            ]
        ],
        'leaderboard' => [
            'enabled' => env('CONTENT_LEADERBOARD_ENABLED', TRUE)
        ],
        'social' => json_decode(env('CONTENT_SOCIAL', json_encode([
            [
                'title' => 'Send us an email',
                'icon' => 'mdi-at',
                'color' => 'blue-grey darken-1',
                'url' => '/'
            ],
            [
                'title' => 'Follow us on Facebook',
                'icon' => 'mdi-facebook',
                'color' => '#4267B2',
                'url' => '/'
            ],
            [
                'title' => 'Follow us on Instagram',
                'icon' => 'mdi-instagram',
                'color' => '#C13584',
                'url' => '/'
            ],
            [
                'title' => 'Follow us on Twitter',
                'icon' => 'mdi-twitter',
                'color' => '#00acee',
                'url' => '/'
            ],
            [
                'title' => 'Follow us on Twitch',
                'icon' => 'mdi-twitch',
                'color' => '#6441a5',
                'url' => '/'
            ]
        ]))),
        'footer' => [
            'menu' => json_decode(env('CONTENT_FOOTER_MENU', json_encode([
                [
                    'id' => 'provably-fair',
                    'title' => 'Provably fair'
                ],
                [
                    'id' => 'privacy-policy',
                    'title' => 'Privacy policy'
                ],
                [
                    'id' => 'terms-of-use',
                    'title' => 'Terms of use'
                ],
            ])))
        ]
    ],
];
