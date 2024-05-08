<?php

namespace ClaraLeigh\AutotweetForLaravel\Exceptions;

use Exception;

class InvalidStateException extends Exception
{
    public function __construct($message = 'Invalid security state.', $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
