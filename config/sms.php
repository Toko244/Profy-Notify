<?php

return [
    'smsco' => [
        'username' => env('SMSCO_USERNAME'),
        'password' => env('SMSCO_PASSWORD'),
        'url' => env('SMSCO_URL'),
        'endpoint_send_sms' => env('SMSCO_ENDPOINT_SEND_SMS', '/sendsms.php'),
    ],

    'error_codes' => [
        '2904' => 'SMS Sending Failed',
        '2905' => 'Invalid username/password combination',
        '2906' => 'Credit exhausted',
        '2907' => 'Gateway unavailable',
        '2908' => 'Invalid schedule date format',
        '2909' => 'Unable to schedule',
        '2910' => 'Username is empty',
        '2911' => 'Password is empty',
        '2912' => 'Recipient is empty',
        '2913' => 'Message is empty',
        '2914' => 'Sender is empty',
        '2915' => 'One or more required fields are empty',
    ]
];
