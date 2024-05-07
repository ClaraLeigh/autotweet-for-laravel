<?php

namespace ClaraLeigh\XForLaravel;

use ClaraLeigh\XForLaravel\Exceptions\CouldNotSendNotification;
use Illuminate\Support\Collection;
use Kylewm\Brevity\Brevity;

class TwitterStatusUpdate extends TwitterMessage
{
    public ?Collection $imageIds = null;

    private ?array $images = null;

    /**
     * @throws CouldNotSendNotification
     */
    public function __construct(string $content)
    {
        parent::__construct($content);

        if ($exceededLength = $this->messageIsTooLong(new Brevity())) {
            throw CouldNotSendNotification::statusUpdateTooLong($exceededLength);
        }
    }

    public function getApiEndpoint(): string
    {
        return 'tweets';
    }

    /**
     * Set Twitter media files.
     *
     * @return $this
     */
    public function withImage(array|string $images): static
    {
        $images = is_array($images) ? $images : [$images];

        collect($images)->each(function ($image) {
            $this->images[] = TwitterImage::createFromString($image);
        });

        return $this;
    }

    /**
     * Get Twitter images list.
     */
    public function getImages(): ?array
    {
        return $this->images;
    }

    /**
     * Build Twitter request body.
     */
    public function getRequestBody(): array
    {
        $body = ['text' => $this->getContent()];

        $mediaIds = $this->imageIds instanceof Collection ? $this->imageIds->values() : collect();

        if ($mediaIds->count() > 0) {
            $body['media'] = [
                'media_ids' => $mediaIds->toArray(),
            ];
        }

        return $body;
    }

    /**
     * Check if the message length is too long.
     *
     * @return int How many characters the max length is exceeded or 0 when it isn't.
     */
    private function messageIsTooLong(Brevity $brevity): int
    {
        $tweetLength = $brevity->tweetLength($this->content);
        $exceededLength = $tweetLength - 280;

        return max($exceededLength, 0);
    }
}
