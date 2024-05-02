<?php

namespace ClaraLeigh\XForLaravel\Http\Controllers;

use ClaraLeigh\XForLaravel\Exceptions\InvalidStateException;
use ClaraLeigh\XForLaravel\Services\TwitterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use Random\RandomException;

class TwitterAuthController extends Controller
{
    /**
     * Redirect the user to the Twitter authentication page.
     *
     *
     * @throws RandomException
     */
    public function redirectToTwitter(TwitterService $service): RedirectResponse
    {
        return Redirect::to(
            $service->prepareAuthorizationUrl()
        );
    }

    /**
     * Obtain the user information from Twitter after authentication.
     *
     *
     * @throws InvalidStateException
     */
    public function handleTwitterCallback(Request $request, TwitterService $service): RedirectResponse
    {
        $service->handleCallback(
            state: $request->get('state'),
            code: $request->get('code')
        );

        return Redirect::to(
            path: config('services.x-for-laravel.redirect_path')
        )->with(
            key: 'status',
            value: 'Twitter account successfully connected!'
        );
    }
}
