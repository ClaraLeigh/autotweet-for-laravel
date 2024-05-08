<?php

namespace ClaraLeigh\AutotweetForLaravel\Events;

use ClaraLeigh\AutotweetForLaravel\Models\TweetLog;
use Illuminate\Foundation\Events\Dispatchable;

class TweetPosted
{
    use Dispatchable;

    public function __construct(
        public mixed $notifiable,
        public TweetLog $tweetLog
    ) {
    }
}
