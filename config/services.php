<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],


    'antigravity' => [
        'key' => env('ANTIGRAVITY_API_KEY', 'sk-a9a37f311b1527fb-r3j1ok-6dcdb951'),
        'url' => env('ANTIGRAVITY_BASE_URL', 'http://localhost:20128/v1'),
        'model' => env('ANTIGRAVITY_MODEL', 'ag/gemini-3.8-flash-low'),
        'timeout' => (int) env('ANTIGRAVITY_TIMEOUT', 60),
    ],

];
