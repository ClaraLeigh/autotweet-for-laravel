<?php

use ClaraLeigh\AutotweetForLaravel\Http\Controllers\TwitterAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('autotweet-for-laravel.middleware'))->group(function () {
    Route::get(
        uri: 'oauth2/twitter',
        action: [TwitterAuthController::class, 'redirectToTwitter']
    )->name('twitter.authorize');

    Route::get(
        uri: 'oauth2/twitter/revoke',
        action: [TwitterAuthController::class, 'revokeAccess']
    )->name('twitter.revoke');

    Route::get(
        uri: 'oauth2/twitter/callback',
        action: [TwitterAuthController::class, 'handleTwitterCallback']
    )->name('twitter.callback');
});
