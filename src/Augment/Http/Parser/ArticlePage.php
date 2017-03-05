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
class ArticlePage extends ParsedObject implements ArticlePageContract
{
    /**
     * @inheritdoc
     */
    public function toc($value = null)
    {
        return parent::toc($value);
    }

    /**
     * @inheritdoc
     */
    public function getToc()
    {
        return $this->toc;
    }

    /**
     * @inheritdoc
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @inheritdoc
     */
    public function getTocText()
    {
        return $this->toctext;
    }

    /**
     * @inheritdoc
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return sprintf(
            "Length: %s pages; Abstract: %s chars; TOC: %d top-level sections in %d chars",
            $this->getLength(),
            strlen($this->getAbstract()),
            count($this->getToc()),
            strlen($this->getTocText())
        );
    }
}
