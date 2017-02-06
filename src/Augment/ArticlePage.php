<?php

namespace Profounder\Augment;

use Illuminate\Support\Fluent;

/**
 * @property  array $toc
 * @property  int $length
 * @property  string $abstract
 * @property  string $toctext
 */
class ArticlePage extends Fluent
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

    /**
     * Prints the object.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            "Length: %s pages; Abstract: %s chars; TOC: %d top-level sections in %d chars",
            $this->length,
            strlen($this->abstract),
            count($this->toc),
            strlen($this->toctext)
        );
    }
}
