<?php

namespace ClaraLeigh\XForLaravel;

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
}
