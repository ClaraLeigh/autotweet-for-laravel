<?php

use ClaraLeigh\XForLaravel\Http\Controllers\TwitterAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get(
        uri: 'auth/twitter',
        action: [TwitterAuthController::class, 'redirectToTwitter']
    )->name('twitter.login');
    Route::get(
        uri: 'auth/twitter/callback',
        action: [TwitterAuthController::class, 'handleTwitterCallback']
    )->name('twitter.callback');
});
