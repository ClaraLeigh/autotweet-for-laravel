<?php

declare(strict_types=1);

namespace ClaraLeigh\XForLaravel\Services;

use ClaraLeigh\XForLaravel\Exceptions\InvalidStateException;
use ClaraLeigh\XForLaravel\XForLaravelServiceProvider;
use Random\RandomException;

class TwitterService
{
    /**
     * Prepare the authorization URL.
     *
     * @return string
     * @throws RandomException
     */
    public function prepareAuthorizationUrl(): string
    {
        $state = $this->state();
        $parameters = [
            'scopes' => 'tweet.read users.read tweet.write offline.access',
            'state' => $state, // This is a random string that you should validate when the user is redirected back to your app, to prevent CSRF attacks
            'response_type' => 'code',
            'client_id' => config('x-for-laravel.consumer_key'),
            'redirect_uri' => config('x-for-laravel.callback_url'),
            'code_challenge' => $this->codeChallenge($state),
            'code_challenge_method' => 'S256',
        ];
        session(['twitter_state' => $state]);

        return 'https://api.twitter.com/oauth2/authorize?'.http_build_query($parameters);
    }

    /**
     * Generate a random state.
     *
     * @return string
     * @throws RandomException
     */
    protected function state(): string
    {
        // CSRF protection
        return bin2hex(random_bytes(16));
    }


    /**
     * Generate a code challenge.
     *
     * @param  string  $str
     *
     * @return string
     */
    protected function codeChallenge(string $str): string
    {
        // sha256 hash of the str
        $hash = hash('sha256', $str, true);

        return rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    }

    /**
     * Handle the callback from Twitter.
     *
     * @param  string  $state
     * @param  string  $code
     *
     * @return void
     * @throws InvalidStateException
     */
    public function handleCallback(string $state, string $code): void
    {
        // Validate the state
        if ($state !== session('twitter_state')) {
            throw new InvalidStateException();
        }

        $model = app(XForLaravelServiceProvider::$userModel);
        $model->twitter_token = $code;
        $model->save();
    }
}
