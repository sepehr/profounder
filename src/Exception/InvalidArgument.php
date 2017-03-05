<?php

namespace Profounder\Exception;

class InvalidArgument extends Exception
{

    /**
     * Static factory method for not-found entities.
     *
     * @param string|null $message
     *
     * @return InvalidArgument
     */
    public static function notFound($message = null)
    {
        return new static($message ?: 'Entity could not be found.');
    }
}
