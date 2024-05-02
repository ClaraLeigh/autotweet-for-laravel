<?php

return [
    'consumer_key' => env('TWITTER_CONSUMER_KEY'),
    'consumer_secret' => env('TWITTER_CONSUMER_SECRET'),
    'access_token' => env('TWITTER_ACCESS_TOKEN'),
    'access_secret' => env('TWITTER_ACCESS_SECRET'),
    'callback_url' => env('X_CALLBACK_URL', ''),
    'redirect_url' => '/auth/twitter/callback',
    'permissions' => env('X_CALLBACK_URL', 'read,write'),
];
