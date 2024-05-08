<?php

namespace ClaraLeigh\AutotweetForLaravel\Models;

use Illuminate\Database\Eloquent\Model;

class TweetLog extends Model
{
    protected $fillable = [
        'tweet_id',
        'user_id',
        'content',
        'images',
    ];

    /**
     * Casts for the model
     * Note: for privacy, we are encrypting the response fields
     *
     * @var string[]
     */
    protected $casts = [
        'images' => 'array',
    ];
}
