<?php

return [
    'client_id' => env('TWITTER_CLIENT_ID'),
    'client_secret' => env('TWITTER_CLIENT_SECRET'),
    'debug' => env('TWITTER_DEBUG', env('APP_DEBUG', false)),
    // This is used to set the url to redirect to after login
    'redirect_path' => env('TWITTER_REDIRECT_PATH', '/'),
    // Your Twitter Route Middleware
    'middleware' => ['web', 'guard:customer', 'auth:customer'],
];
