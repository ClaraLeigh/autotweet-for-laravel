<?php

namespace ClaraLeigh\AutotweetForLaravel;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

readonly class TwitterImage
{
    /**
     * TwitterImage constructor.
     */
    public function __construct(private string $imagePath)
    {
    }

    /**
     * Get the image path.
     */
    public function getPath(): string
    {
        return $this->imagePath;
    }

    public static function createFromString($imagePath): TwitterImage
    {
        if (Str::contains($imagePath, 'http')) {
            $path = 'twitter/'.uuid_create().'.jpg';
            $image = Storage::put($path, file_get_contents($imagePath), 'public');

            return new TwitterImage($path);
        }

        return new TwitterImage($imagePath);
    }
}
