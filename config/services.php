<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |

    IP Address  "104.130.122.30"

API Key  "d86cd5354c88cf2680e09062fe366d32-9525e19d-b3bc67ce"

API Base URL  "https://api.mailgun.net/v3/texo.vn"

SMTP Hostname  "smtp.mailgun.org"

SMTP Login "news@texo.vn"

Pass  "1234@abcd"
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN','minhphucgroup.com.vn'),
        'secret' => env('MAILGUN_SECRET','d86cd5354c88cf2680e09062fe366d32-9525e19d-b3bc67ce'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

];
