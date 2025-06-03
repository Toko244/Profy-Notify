<?php

return [
    /*
    |--------------------------------------------------------------------------
    | One Signal App Configs
    |--------------------------------------------------------------------------
    |
    | Define separate configs for customer and tasker apps.
    |
    */

    'tasker' => [
        'app_id' => env('ONESIGNAL_TASKER_APP_ID'),
        'api_key' => env('ONESIGNAL_TASKER_API_KEY'),
    ],

    'customer' => [
        'app_id' => env('ONESIGNAL_CUSTOMER_APP_ID'),
        'api_key' => env('ONESIGNAL_CUSTOMER_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Shared Options
    |--------------------------------------------------------------------------
    */

    'rest_api_url' => env('ONESIGNAL_REST_API_URL', 'https://api.onesignal.com'),
    'user_auth_key' => env('USER_AUTH_KEY'),
    'guzzle_client_timeout' => env('ONESIGNAL_GUZZLE_CLIENT_TIMEOUT', 0),
];
