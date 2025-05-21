<?php

use ClaraLeigh\AutotweetForLaravel\Http\Controllers\TwitterAuthController;
use ClaraLeigh\AutotweetForLaravel\Services\TwitterService;
use Illuminate\Http\Request;

it('redirects when OAuth is cancelled', function () {
    session(['twitter_state' => 'foo']);
    $request = Request::create('/oauth2/twitter/callback', 'GET', [
        'state' => 'foo',
        'error' => 'access_denied',
    ]);

    $service = Mockery::mock(TwitterService::class);
    $service->shouldNotReceive('handleCallback');

    $controller = new TwitterAuthController();
    $response = $controller->handleTwitterCallback($request, $service);

    expect($response->getTargetUrl())->toBe(url('/'));
    expect(session('error'))->toBe('Twitter authentication cancelled.');
});
