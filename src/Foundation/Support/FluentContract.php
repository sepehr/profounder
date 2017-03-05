<?php
namespace Profounder\Foundation\Support;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

interface FluentContract extends \ArrayAccess, \JsonSerializable, Arrayable, Jsonable
{
    /**
     * Static factory method.
     *
     * @param  array  ...$args
     *
     * @return static
     */
    public static function create(...$args);
}
