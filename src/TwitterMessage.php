<?php

namespace ClaraLeigh\AutotweetForLaravel;

abstract class TwitterMessage
{
    public bool $isJsonRequest = true;

    public function __construct(protected string $content) {}

    public function getContent(): string
    {
        return $this->content;
    }

    abstract public function getApiEndpoint(): string;
}
