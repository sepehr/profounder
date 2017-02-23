<?php

namespace Profounder;

use Illuminate\Support\Fluent;

/**
 * Class representing a request state data and cookie.
 *
 * @property  array $data
 * @property  array $cookie
 */
class State extends Fluent
{
    /**
     * Static factory method.
     *
     * @param  array $args
     *
     * @return $this
     */
    public static function create(...$args)
    {
        return new static(...$args);
    }
}
