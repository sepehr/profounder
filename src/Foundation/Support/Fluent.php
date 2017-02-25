<?php

namespace Profounder\Foundation\Support;

class Fluent extends \Illuminate\Support\Fluent
{
    /**
     * Static factory method.
     *
     * @param  array  ...$args
     *
     * @return static
     */
    public static function create(...$args)
    {
        return new static(...$args);
    }
}
