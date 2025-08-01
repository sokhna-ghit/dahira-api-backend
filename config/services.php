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

    'orange' => [
        'client_id' => env('ORANGE_CLIENT_ID'),
        'client_secret' => env('ORANGE_CLIENT_SECRET'),
        'api_key' => env('ORANGE_API_KEY'),
        'base_url' => env('ORANGE_BASE_URL', 'https://api.orange.com'),
        'country_code' => env('ORANGE_COUNTRY_CODE', 'SN'),
        'environment' => env('ORANGE_ENVIRONMENT', 'sandbox'),
    ],

    'paydunya' => [
        'master_key' => env('PAYDUNYA_MASTER_KEY'),
        'private_key' => env('PAYDUNYA_PRIVATE_KEY'),
        'public_key' => env('PAYDUNYA_PUBLIC_KEY'),
        'token' => env('PAYDUNYA_TOKEN'),
        'mode' => env('PAYDUNYA_MODE', 'test'),
        'base_url' => env('PAYDUNYA_BASE_URL', 'https://app.paydunya.com'),
    ],

];
