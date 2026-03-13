<?php

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'personnel'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'personnel'),
    ],

    'guards' => [
        'personnel' => [
            'driver' => 'sanctum',
            'provider' => 'personnels',
        ],

        'web' => [
            'driver' => 'session',
            'provider' => 'personnels',
        ],
    ],

    'providers' => [
        'personnels' => [
            'driver' => 'eloquent',
            'model' => App\Models\Personnel::class,
        ],
    ],

    'passwords' => [
        'personnel' => [
            'provider' => 'personnels',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];
