<?php

namespace Profounder\Foundation\Support;

use Illuminate\Support\Fluent as IlluminateFluent;

class Fluent extends IlluminateFluent implements FluentContract
{
    /**
     * @inheritdoc
     */
    public static function create(...$args)
    {
        return new static(...$args);
    }
}
