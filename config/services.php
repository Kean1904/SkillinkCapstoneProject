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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'brevo' => [
        'key' => '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',
        'sender_email' => 'torreskeanashleym2021@gmail.com',
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY', 're_jDuKdBU7_EdBofh4jN8Tmb1t4gBGn1WUB'),
    ],

];
