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

    /*
    | Local GSM modem bridge (Flask sms_server.py). POST JSON array of
    | { "number", "message" } to /send-sms with X-API-KEY header.
    */
    'sms_modem' => [
        'url' => env('SMS_MODEM_URL', 'http://127.0.0.1:5000/send-sms'),
        'api_key' => env('SMS_MODEM_API_KEY', 'library123'),
    ],

    /*
    | Optional API key for Google Books (higher quota). ISBN lookup works without a key
    | with stricter rate limits — useful for copy cataloging Philippine / regional titles.
    */
    'google_books' => [
        'key' => env('GOOGLE_BOOKS_API_KEY'),
    ],

];
