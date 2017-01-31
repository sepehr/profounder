<?php

namespace Profounder\Exception;

class InvalidSession extends BaseException
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
}
