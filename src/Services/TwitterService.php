<?php

declare(strict_types=1);

namespace ClaraLeigh\XForLaravel\Services;

use Abraham\TwitterOAuth\TwitterOAuth;
use Carbon\Carbon;
use ClaraLeigh\XForLaravel\Exceptions\InvalidStateException;
use ClaraLeigh\XForLaravel\XForLaravelServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Random\RandomException;

class TwitterService
{
    public function __construct(public TwitterOAuth $api)
    {
    }

    /**
     * Prepare the authorization URL.
     *
     * @throws RandomException
     */
    public function prepareAuthorizationUrl(): string
    {
        $state = $this->state();
        $parameters = [
            'scope' => 'tweet.read users.read tweet.write offline.access', // 'tweet.read users.read tweet.write offline.access
            'response_type' => 'code',
            'client_id' => config('x-for-laravel.client_id'),
            'redirect_uri' => url()->route('twitter.callback'),
            'state' => $state, // This is a random string that you should validate when the user is redirected back to your app, to prevent CSRF attacks
            'code_challenge' => $state, // 'challenge', //$this->codeChallenge($state), //'S256'
            'code_challenge_method' => 'plain',
        ];
        session(['twitter_state' => $state]);

        return 'https://twitter.com/i/oauth2/authorize?'.http_build_query($parameters);
    }

    /**
     * Generate a random state.
     *
     * @throws RandomException
     */
    protected function state(): string
    {
        // CSRF protection
        return bin2hex(random_bytes(16));
    }

    /**
     * Generate a code challenge.
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
     *
     *
     * @throws ConnectionException
     * @throws InvalidStateException
     * @throws RequestException
     */
    public function handleCallback(string $state, string $code): void
    {
        // Validate the state
        if ($state !== session('twitter_state')) {
            throw new InvalidStateException();
        }

        // Store the refresh token and get access token
        $response = $this->getAccessTokenFromAuth($state, $code);
        if (config('x-for-laravel.debug')) {
            Log::info('Twitter callback - access token data:', [
                'response' => $response,
            ]);
        }

        $model = app(XForLaravelServiceProvider::$userModel)->find(auth()->id());
        $model->twitter_token = (object) [
            'scope' => $response['scope'],
            'access_token' => $response['access_token'],
            'refresh_token' => $response['refresh_token'],
            'expires_in' => now()->addSeconds($response['expires_in']),
        ];
        $model->save();
    }

    /**
     * Get the access token from the returned code
     *
     *
     * @return array ['token_type', 'expires_in', 'access_token', 'scope', 'refresh_token']
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function getAccessTokenFromAuth(string $state, string $code): array
    {
        return Http::asForm()
            ->withHeaders([
                'Authorization' => 'Basic '.base64_encode(config('x-for-laravel.client_id').':'.config('x-for-laravel.client_secret')),
            ])
            ->post(
                url: 'https://api.twitter.com/2/oauth2/token',
                data: [
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                    'code_verifier' => $state,
                    'client_id' => config('x-for-laravel.client_id'),
                    'redirect_uri' => url()->route('twitter.callback'),
                ]
            )
            ->throw()
            ->json();
    }

    /**
     * Fetch or update the access token.
     */
    public function fetchOrUpdateAccessToken(Model $model): string
    {
        $token = $model->twitter_token;
        $expires = Carbon::createFromTimestamp($token->expires_in);
        if ($expires->isAfter(now()->addMinutes(5))) {
            $response = $this->refreshAccessToken($token->refresh_token);
            if (config('x-for-laravel.debug')) {
                Log::info('Twitter callback - access token data:', [
                    'response' => $response,
                ]);
            }
            $model->twitter_token = (object) [
                'scope' => $response['scope'],
                'access_token' => $response['access_token'],
                'refresh_token' => $response['refresh_token'],
                'expires_in' => now()->addSeconds($response['expires_in']),
            ];
            $model->save();
        }

        return $model->twitter_token->access_token;
    }

    /**
     * Refresh the access token.
     *
     * @return array|mixed
     *
     * @throws ConnectionException
     * @throws RequestException
     */
    public function refreshAccessToken($refresh_token)
    {
        return Http::asForm()
            ->withHeaders([
                'Authorization' => 'Basic '.base64_encode(config('x-for-laravel.client_id').':'.config('x-for-laravel.client_secret')),
            ])
            ->post(
                url: 'https://api.twitter.com/2/oauth2/token',
                data: [
                    'refresh_token' => $refresh_token,
                    'grant_type' => 'refresh_token',
                ]
            )->throw()
            ->json();
    }

    /**
     * Revoke the access token.
     *
     *
     * @throws ConnectionException
     */
    public function revoke($token): void
    {
        try {
            Http::asForm()
                ->withHeaders([
                    'Authorization' => 'Basic '.base64_encode(config('x-for-laravel.client_id').':'.config('x-for-laravel.client_secret')),
                ])
                ->post(
                    url: 'https://api.twitter.com/2/oauth2/revoke',
                    data: [
                        'token' => $token,
                    ]
                );
        } catch (RequestException $e) {
            // Fail Silently
            if (config('x-for-laravel.debug')) {
                Log::error('Twitter revoke failed:', [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
