<?php

namespace Profounder\Exception;

class InvalidSession extends Exception
{
    /**
     * Static factory method for expired sessions.
     *
     * @return InvalidSession
     */
    public static function expired()
    {
        return new static(
            'Either the session has expired or its trial period is over. Feed me some fresh sessions.'
        );
    }

    /**
     * Static factory method for not existing sessions.
     *
     * @return InvalidSession
     */
    public static function notFound()
    {
        return new static('Session file not found; try logging in first.');
    }
}
