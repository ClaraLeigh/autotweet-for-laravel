<?php

namespace ClaraLeigh\XForLaravel\Http\Controllers;

use ClaraLeigh\XForLaravel\Exceptions\InvalidStateException;
use ClaraLeigh\XForLaravel\Services\TwitterService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
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
            path: $service->prepareAuthorizationUrl()
        );
    }

    public function revokeAccess(TwitterService $service): RedirectResponse
    {
        $user = auth()->user();
        if (! empty($user->twitter_token)) {
            $service->revoke($user->twitter_token->refresh_token);
            $user->twitter_token = null;
            $user->save();
        }

        return Redirect::to(
            path: config('x-for-laravel.redirect_path')
        )->with(
            key: 'status',
            value: 'Twitter account successfully disconnected!'
        );
    }

    /**
     * Obtain the user information from Twitter after authentication.
     *
     *
     *
     * @throws InvalidStateException
     * @throws ConnectionException
     * @throws RequestException
     */
    public function handleTwitterCallback(Request $request, TwitterService $service): RedirectResponse
    {
        $service->handleCallback(
            state: $request->get('state'),
            code: $request->get('code')
        );

        return Redirect::to(
            path: config('x-for-laravel.redirect_path')
        )->with(
            key: 'status',
            value: 'Twitter account successfully connected!'
        );
    }
}
