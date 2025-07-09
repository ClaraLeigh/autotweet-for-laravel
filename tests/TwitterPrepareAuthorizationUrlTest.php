<?php

use ClaraLeigh\AutotweetForLaravel\Services\TwitterService;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

it('stores state and verifier when building redirect URL', function () {
    Http::fake();
    $service = new TwitterService(Mockery::mock(TwitterOAuth::class));

    $url = $service->prepareAuthorizationUrl();

    expect(session('twitter_state'))->not()->toBeNull();
    expect(session('twitter_code_verifier'))->not()->toBeNull();

    parse_str(Str::after($url, '?'), $params);

    expect($params['state'])->toBe(session('twitter_state'));
    expect($params['code_challenge'])->not()->toBe(session('twitter_state'));
    expect($params['code_challenge_method'])->toBe('S256');
});
