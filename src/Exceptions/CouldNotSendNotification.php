<?php

namespace ClaraLeigh\AutotweetForLaravel\Exceptions;

use Exception;

class CouldNotSendNotification extends Exception
{
    public static function serviceRespondsNotSuccessful(mixed $response): CouldNotSendNotification
    {
        if (isset($response->error)) {
            return new self("Couldn't post notification. Response: ".$response->error);
        }

        if (empty($response->detail)) {
            return new self("Couldn't post notification.");
        }

        $responseBody = print_r($response->detail, true);

        return new self("Couldn't post notification. Response: ".$responseBody);
    }

    public static function statusUpdateTooLong(int $exceededLength): CouldNotSendNotification
    {
        return new self(
            "Couldn't post status. Exceeded length by $exceededLength character(s)."
        );
    }
}
