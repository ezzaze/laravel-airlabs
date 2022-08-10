<?php

namespace Ezzaze\Airlabs\Exceptions;

use Exception;

class AirlabsException extends Exception
{
    public static function writeError(string $errorCode): self
    {
        $message = match ($errorCode) {
            'unknown_api_key' => 'Provided API Key is invalid.',
            'expired_api_key' => 'The provided API key has expired.',
            'unknown_method' => 'Provided method is not supported.',
            'wrong_params' => 'Some parameters are wrong.',
            'not_found' => 'Requested data was not found.',
            'minute_limit_exceeded' => 'The number of requests per minute has been exceeded.',
            'hour_limit_exceeded' => 'The number of requests per hour has been exceeded.',
            'month_limit_exceeded' => 'The number of requests per month has been exceeded.',
            default => 'An internal error occurred.',
        };

        return new static($message);
    }
}
