<?php

namespace Profounder\Auth\Session;

use Illuminate\Support\Fluent;

/**
 * Class representing an authenticated session.
 *
 * @property  array $cookie
 */
class Session extends Fluent
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
