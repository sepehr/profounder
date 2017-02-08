<?php

namespace Profounder\Exception;

class InvalidResponse extends Exception
{
    /**
     * Static factory method for invalid JSON responses.
     *
     * @return InvalidResponse
     */
    public static function invalidJson()
    {
        return new static('Invalid JSON response.');
    }

    /**
     * Static factory method for critical error responses.
     *
     * @return InvalidResponse
     */
    public static function critical()
    {
        return new static('Remote webserver critical hiccup! renew the session to work around this.');
    }

    /**
     * Static factory method for not-found responses.
     *
     * @return InvalidResponse
     */
    public static function notFound()
    {
        return new static('Remote entity could not be found.');
    }

    /**
     * Static factory method for invalid responses with custom remote error message.
     *
     * @param  string $error
     *
     * @return InvalidResponse
     */
    public static function remoteError($error)
    {
        return new static("Remote error: $error");
    }
}
