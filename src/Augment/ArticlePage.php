<?php

namespace Profounder\Augment;

class ArticlePage
{
    /**
     * Article TOC.
     *
     * @var array
     */
    public $toc;

    /**
     * Article flat TOC.
     *
     * @var string
     */
    public $flatToc;

    /**
     * Article length.
     *
     * @var int|null
     */
    public $length;

    /**
     * Article abstract.
     *
     * @var string|null
     */
    public $abstract;

    /**
     * ArticlePage constructor.
     *
     * @param  array|null $toc
     * @param  string|null $flatToc
     * @param  int|null $length
     * @param  string|null $abstract
     */
    public function __construct(array $toc = null, $flatToc = null, $length = null, $abstract = null)
    {
        $this->toc      = $toc;
        $this->flatToc  = $flatToc;
        $this->length   = $length;
        $this->abstract = $abstract;
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
            strlen($this->flatToc)
        );
    }
}
