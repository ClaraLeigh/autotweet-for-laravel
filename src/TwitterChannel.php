<?php

namespace ClaraLeigh\XForLaravel;

use Abraham\TwitterOAuth\TwitterOAuth;
use ClaraLeigh\XForLaravel\Events\TweetPosted;
use ClaraLeigh\XForLaravel\Exceptions\CouldNotSendNotification;
use ClaraLeigh\XForLaravel\Models\TweetLog;
use ClaraLeigh\XForLaravel\Services\TwitterService;
use Illuminate\Notifications\Notification;

class TwitterChannel
{
    public function __construct(protected TwitterService $service)
    {
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable  Should be an object that uses the Illuminate\Notifications\Notifiable trait.
     *
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification): void
    {
        if (empty($notifiable->twitter_token) || ! method_exists($notification, 'toTwitter')) {
            return;
        }

        $this->service->api->setBearer(
            $this->service->fetchOrUpdateAccessToken($notifiable)
        );

        $twitterMessage = $notification->toTwitter($notifiable);
        $twitterMessage = $this->addImagesIfGiven($twitterMessage);

        $requestBody = $twitterMessage->getRequestBody();

        $twitterApiResponse = $this->service->api->post(
            path: $twitterMessage->getApiEndpoint(),
            parameters: $requestBody,
            options: [
                'jsonPayload' => $twitterMessage->isJsonRequest,
            ]
        );

        if ($this->service->api->getLastHttpCode() !== 201) {
            throw CouldNotSendNotification::serviceRespondsNotSuccessful($this->service->api->getLastBody());
        }

        $tweetLog = new TweetLog();
        $tweetLog->fill([
            'tweet_id' => $twitterApiResponse->data->id,
            'user_id' => $notifiable->id,
            'content' => $twitterMessage->getContent(),
            'response' => $twitterApiResponse,
        ]);
        $tweetLog->save();

        event(new TweetPosted(
            notifiable: $notifiable,
            tweetLog: $tweetLog
        ));
    }

    /**
     * If it is a status update message and images are provided, add them.
     */
    private function addImagesIfGiven(TwitterMessage $twitterMessage): object
    {
        if (is_a($twitterMessage, TwitterStatusUpdate::class) && $twitterMessage->getImages()) {
            $this->service->api->setTimeouts(10, 15);

            $twitterMessage->imageIds = collect($twitterMessage->getImages())
                ->map(function (TwitterImage $image) {
                    $media = $this->service->api->upload(
                        path: 'media/upload',
                        parameters: ['media' => $image->getPath()]
                    );

                    return $media->media_id_string;
                });
        }

        return $twitterMessage;
    }
}
