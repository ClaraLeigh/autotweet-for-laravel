<?php

return [
    'client_id' => env('TWITTER_CLIENT_ID'),
    'client_secret' => env('TWITTER_CLIENT_SECRET'),
    'debug' => env('TWITTER_DEBUG', env('APP_DEBUG', false)),
    'redirect_path' => '/home',

    'consumer_key' => env('TWITTER_CONSUMER_KEY'),
    'consumer_secret' => env('TWITTER_CONSUMER_SECRET'),
    'access_token' => env('TWITTER_ACCESS_TOKEN'),
    'access_secret' => env('TWITTER_ACCESS_SECRET'),
    'callback_url' => env('TWITTER_CALLBACK_URL'),
    'redirect_url' => '/auth/twitter/callback',
];
