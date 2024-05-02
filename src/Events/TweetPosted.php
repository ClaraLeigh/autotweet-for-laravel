<?php

namespace ClaraLeigh\XForLaravel\Events;

use ClaraLeigh\XForLaravel\Models\TweetLog;
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
