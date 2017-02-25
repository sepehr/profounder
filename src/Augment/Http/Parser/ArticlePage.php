<?php

namespace Profounder\Augment\Http\Parser;

use Profounder\Foundation\Http\Parser\ParsedObject;

/**
 * Class representing an article page.
 *
 * @property  array $toc
 * @property  int $length
 * @property  string $abstract
 * @property  string $toctext
 */
class ArticlePage extends ParsedObject
{
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
