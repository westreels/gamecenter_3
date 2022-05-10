<?php

return [

    'version' => '1.15.0',

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Stake'),

    /*
    |--------------------------------------------------------------------------
    | Application logo
    |--------------------------------------------------------------------------
    |
    */

    'logo' => env('APP_LOGO', '/images/logo/logo.png'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => env('LOCALE', 'en'),

    // separate variable is required to set the default language,
    // because app.locale is set by the SetLocale middleware depending on the language passed in HTTP headers
    'default_locale' => env('LOCALE', 'en'),

    'detect_browser_locale' => env('DETECT_BROWSER_LOCALE', TRUE),

    'locales' => [
        'en' => [
            'flag'  => 'us',
            'title' => 'English'
        ],
        'ru' => [
            'title' => 'Русский'
        ],
        'de' => [
            'title' => 'Deutsch',
        ],
        'es' => [
            'title' => 'Español',
        ],
        'fr' => [
            'title' => 'Français',
        ],
        'pt' => [
            'title' => 'Português',
        ],
        'nl' => [
            'title' => 'Nederlands',
        ],
        'cs' => [
            'flag'  => 'cz',
            'title' => 'Česky',
        ],
        'it' => [
            'title' => 'Italiano',
        ],
        'fi' => [
            'title' => 'Suomi',
        ],
        'sv' => [
            'flag'  => 'se',
            'title' => 'Svenska',
        ],
        'hu' => [
            'title' => 'Magyar',
        ],
        'el' => [
            'flag'  => 'gr',
            'title' => 'Ελληνικά',
        ],
        'da' => [
            'flag'  => 'dk',
            'title' => 'Dansk',
        ],
        'lv' => [
            'title' => 'Latviešu',
        ],
        'lt' => [
            'title' => 'Lietuvių',
        ],
        'et' => [
            'flag'  => 'ee',
            'title' => 'Eesti',
        ],
        'sk' => [
            'title' => 'Slovenčina',
        ],
        'sl' => [
            'flag'  => 'si',
            'title' => 'Slovenščina',
        ],
        'ko' => [
            'flag'  => 'kr',
            'title' => '한국어',
        ],
        // simplified chinese
        'zh-cn' => [
            'flag'  => 'cn',
            'title' => '简体',
        ],
        // traditional chinese
        'zh-tw' => [
            'flag'  => 'tw',
            'title' => '繁体',
        ],
        'ja' => [
            'flag'  => 'jp',
            'title' => '日本語',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    'hash' => 'da34b0aa975fddd5a90a1eb2bb8cb9d2',

    /*
    |--------------------------------------------------------------------------
    | API
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'api' => [
        'releases' => [
            'base_url' => env('API_RELEASES_BASE_URL', 'https://stake.financialplugins.com/api/')
        ],
        'products' => [
            'base_url' => env('API_PRODUCTS_BASE_URL', 'https://financialplugins.com/api/')
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */
        SocialiteProviders\Manager\ServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        // 'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,

    ],

    /*
     * Hide certain variables from debug screen
     */
    'debug_blacklist' => [
        '_ENV' => [
            'APP_KEY',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD',
            'REDIS_URL',
            'REDIS_HOST',
            'REDIS_PASSWORD',
            'REDIS_PORT',
            'MAIL_HOST',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'PUSHER_APP_SECRET',
            'PURCHASE_CODE',
            'AMERICAN_ROULETTE_PURCHASE_CODE',
            'BLACKJACK_PURCHASE_CODE',
            'CARIBBEAN_POKER_PURCHASE_CODE',
            'CASINO_HOLDEM_PURCHASE_CODE',
            'CRASH_PURCHASE_CODE',
            'CRYPTO_PREDICTION_PURCHASE_CODE',
            'DICE_PURCHASE_CODE',
            'DICE_3D_PURCHASE_CODE',
            'EUROPEAN_ROULETTE_PURCHASE_CODE',
            'HEADS_OR_TAILS_PURCHASE_CODE',
            'HORSE_RACING_PURCHASE_CODE',
            'KENO_PURCHASE_CODE',
            'LUCKY_WHEEL_PURCHASE_CODE',
            'MULTIPLAYER_BLACKJACK_PURCHASE_CODE',
            'MULTIPLAYER_ROULETTE_PURCHASE_CODE',
            'RAFFLE_PURCHASE_CODE',
            'PLINKO_PURCHASE_CODE',
            'SIC_BO_PURCHASE_CODE',
            'SLOTS_PURCHASE_CODE',
            'SLOTS_3D_PURCHASE_CODE',
            'VIDEO_POKER_PURCHASE_CODE',
            'LICENSEE_EMAIL',
            'SECURITY_HASH',
            'FACEBOOK_CLIENT_SECRET',
            'TWITTER_CLIENT_SECRET',
            'LINKEDIN_CLIENT_SECRET',
            'GOOGLE_CLIENT_SECRET',
            'YAHOO_CLIENT_SECRET',
            'COINBASE_CLIENT_SECRET',
            'STEEM_CLIENT_SECRET',
            'RECAPTCHA_SECRET_KEY',
            'PAYMENTS_PAYPAL_USER',
            'PAYMENTS_PAYPAL_PASSWORD',
            'PAYMENTS_PAYPAL_SIGNATURE',
            'PAYMENTS_STRIPE_PUBLIC_KEY',
            'PAYMENTS_STRIPE_SECRET_KEY',
            'PAYMENTS_COINPAYMENTS_MERCHANT_ID',
            'PAYMENTS_COINPAYMENTS_PUBLIC_KEY',
            'PAYMENTS_COINPAYMENTS_PRIVATE_KEY',
            'PAYMENTS_COINPAYMENTS_SECRET_KEY',
            'PAYMENTS_BSC_EXPLORER_API_KEY',
            'PAYMENTS_ETHEREUM_ETHERSCAN_API_KEY',
            'API_CRYPTO_PROVIDERS_CRYPTOCOMPARE_API_KEY',
            'RECAPTCHA_SECRET_KEY',
            'GAME_PROVIDERS_BGAMING_API_ID',
            'GAME_PROVIDERS_BGAMING_API_SECRET_KEY',
            'GAME_PROVIDERS_EVOPLAY_API_ID',
            'GAME_PROVIDERS_EVOPLAY_API_SECRET_KEY',
        ],
        '_SERVER' => [
            'APP_KEY',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD',
            'REDIS_URL',
            'REDIS_HOST',
            'REDIS_PASSWORD',
            'REDIS_PORT',
            'MAIL_HOST',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'PUSHER_APP_SECRET',
            'PURCHASE_CODE',
            'AMERICAN_ROULETTE_PURCHASE_CODE',
            'BLACKJACK_PURCHASE_CODE',
            'CARIBBEAN_POKER_PURCHASE_CODE',
            'CASINO_HOLDEM_PURCHASE_CODE',
            'CRASH_PURCHASE_CODE',
            'CRYPTO_PREDICTION_PURCHASE_CODE',
            'DICE_PURCHASE_CODE',
            'DICE_3D_PURCHASE_CODE',
            'EUROPEAN_ROULETTE_PURCHASE_CODE',
            'HEADS_OR_TAILS_PURCHASE_CODE',
            'HORSE_RACING_PURCHASE_CODE',
            'KENO_PURCHASE_CODE',
            'LUCKY_WHEEL_PURCHASE_CODE',
            'MULTIPLAYER_BLACKJACK_PURCHASE_CODE',
            'MULTIPLAYER_ROULETTE_PURCHASE_CODE',
            'RAFFLE_PURCHASE_CODE',
            'PLINKO_PURCHASE_CODE',
            'SIC_BO_PURCHASE_CODE',
            'SLOTS_PURCHASE_CODE',
            'SLOTS_3D_PURCHASE_CODE',
            'VIDEO_POKER_PURCHASE_CODE',
            'LICENSEE_EMAIL',
            'SECURITY_HASH',
            'FACEBOOK_CLIENT_SECRET',
            'TWITTER_CLIENT_SECRET',
            'LINKEDIN_CLIENT_SECRET',
            'GOOGLE_CLIENT_SECRET',
            'YAHOO_CLIENT_SECRET',
            'COINBASE_CLIENT_SECRET',
            'STEEM_CLIENT_SECRET',
            'RECAPTCHA_SECRET_KEY',
            'PAYMENTS_PAYPAL_USER',
            'PAYMENTS_PAYPAL_PASSWORD',
            'PAYMENTS_PAYPAL_SIGNATURE',
            'PAYMENTS_STRIPE_PUBLIC_KEY',
            'PAYMENTS_STRIPE_SECRET_KEY',
            'PAYMENTS_COINPAYMENTS_MERCHANT_ID',
            'PAYMENTS_COINPAYMENTS_PUBLIC_KEY',
            'PAYMENTS_COINPAYMENTS_PRIVATE_KEY',
            'PAYMENTS_COINPAYMENTS_SECRET_KEY',
            'PAYMENTS_BSC_EXPLORER_API_KEY',
            'PAYMENTS_ETHEREUM_ETHERSCAN_API_KEY',
            'API_CRYPTO_PROVIDERS_CRYPTOCOMPARE_API_KEY',
            'RECAPTCHA_SECRET_KEY',
            'GAME_PROVIDERS_BGAMING_API_ID',
            'GAME_PROVIDERS_BGAMING_API_SECRET_KEY',
            'GAME_PROVIDERS_EVOPLAY_API_ID',
            'GAME_PROVIDERS_EVOPLAY_API_SECRET_KEY',
        ],
    ],
];
