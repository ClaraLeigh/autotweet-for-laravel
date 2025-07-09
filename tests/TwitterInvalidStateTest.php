<?php

use Abraham\TwitterOAuth\TwitterOAuth;
use ClaraLeigh\AutotweetForLaravel\AutotweetForLaravelServiceProvider;
use ClaraLeigh\AutotweetForLaravel\Exceptions\InvalidStateException;
use ClaraLeigh\AutotweetForLaravel\Services\TwitterService;
use Illuminate\Support\Facades\Http;

class StubUser
{
    public $twitter_token;

    public function find($id)
    {
        return $this;
    }

    public function save() {}
}

it('throws an exception when the state is invalid', function () {
    AutotweetForLaravelServiceProvider::useUserModel(StubUser::class);
    session(['twitter_state' => 'expected', 'twitter_code_verifier' => 'verifier']);

    Http::fake();
    $service = new TwitterService(Mockery::mock(TwitterOAuth::class));

    expect(fn () => $service->handleCallback('invalid', 'code'))
        ->toThrow(InvalidStateException::class);
});
