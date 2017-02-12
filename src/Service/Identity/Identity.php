<?php

namespace Profounder\Service\Identity;

use Illuminate\Support\Fluent;

/**
 * @property  array $cookie
 * @property  string $username
 * @property  string $password
 */
class Identity extends Fluent
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
