<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OAuth providers
    |--------------------------------------------------------------------------
    | More can be found at https://socialiteproviders.netlify.com/
    |
    */

    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect'      => '/api/oauth/facebook/callback', // If the redirect option contains a relative path, it will automatically be resolved to a fully qualified URL.
    ],

    // Stateless authentication is not available for the Twitter driver, which uses OAuth 1.0 for authentication.
    'twitter' => [
        'client_id'     => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect'      => '/api/oauth/twitter/callback', // If the redirect option contains a relative path, it will automatically be resolved to a fully qualified URL.
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => '/api/oauth/google/callback', // If the redirect option contains a relative path, it will automatically be resolved to a fully qualified URL.
    ],

    'linkedin' => [
        'client_id'     => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect'      => '/api/oauth/linkedin/callback', // If the redirect option contains a relative path, it will automatically be resolved to a fully qualified URL.
    ],

    'github' => [
        'client_id'     => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect'      => '/api/oauth/github/callback', // If the redirect option contains a relative path, it will automatically be resolved to a fully qualified URL.
        'mdi'           => 'github', // icon
    ],

    'yahoo' => [
        'client_id'     => env('YAHOO_CLIENT_ID'),
        'client_secret' => env('YAHOO_CLIENT_SECRET'),
        'redirect'      => '/api/oauth/yahoo/callback', // If the redirect option contains a relative path, it will automatically be resolved to a fully qualified URL.
    ],

    'coinbase' => [
        'client_id'     => env('COINBASE_CLIENT_ID'),
        'client_secret' => env('COINBASE_CLIENT_SECRET'),
        'redirect'      => '/api/oauth/coinbase/callback', // If the redirect option contains a relative path, it will automatically be resolved to a fully qualified URL.
        'mdi'           => 'circle-multiple', // icon
    ],
];
