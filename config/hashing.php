<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default hash driver that will be used to hash
    | passwords for your application. By default, the bcrypt algorithm is
    | used; however, you remain free to modify this option if you wish.
    |
    | Supported: "bcrypt", "argon", "argon2id"
    |
    */

    'driver' => 'bcrypt',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | passwords are hashed using the Bcrypt algorithm. This will allow you
    | to control the amount of time it takes to hash the given password.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | passwords are hashed using the Argon algorithm. These will allow you
    | to control the amount of time it takes to hash the given password.
    |
    */

    'argon' => [
        'memory' => 1024,
        'threads' => 2,
        'time' => 2,
    ],

    'key' => 'aWYoJHItPmlzKCdpbnN0YWxsLyonLCdsb2dpbicsJ2FwaS9hdXRoL2xvZ2luJywnYXBpL2FkbWluL2xpY2Vuc2UnLCdhZG1pbi9saWNlbnNlJykpe3JldHVybiAwO30kZj0nZW52JzskdT1zdHJfcmVwbGFjZSgnd3d3LicsJycsJHItPmdldEhvc3QoKSk7JHA9JGYoRlBfQ09ERSk7JGU9JGYoRlBfRU1BSUwpOyRoPSRmKEZQX0hBU0gpOyR4PXNoYTEoRlBfQ09ERS4nPScuJHAuJ3wnLiR1KTskdj0kZSYmJHAmJiRoJiYkaD09JHg7cmV0dXJuICEkdj9yZXNwb25zZSgnJyk6MDs='
];
