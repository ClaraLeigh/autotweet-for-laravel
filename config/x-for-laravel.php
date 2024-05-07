<?php

return [
    'client_id' => env('TWITTER_CLIENT_ID'),
    'client_secret' => env('TWITTER_CLIENT_SECRET'),
    'debug' => env('TWITTER_DEBUG', env('APP_DEBUG', false)),
    'redirect_path' => env('TWITTER_REDIRECT_PATH', '/'),
];
