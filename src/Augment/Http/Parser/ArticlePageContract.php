<?php

namespace Profounder\Augment\Http\Parser;

use Profounder\Foundation\Http\Parser\ParsedObjectContract;

interface ArticlePageContract extends ParsedObjectContract
{
    /**
     * Setter for toc property.
     *
     * @param null $value
     *
     * @return $this
     */
    public function toc($value = null);

    /**
     * Getter for toc property.
     *
     * @return array
     */
    public function getToc();

    /**
     * Getter for toctext property.
     *
     * @return string
     */
    public function getTocText();

    /**
     * Getter for length property.
     *
     * @return int
     */
    public function getLength();

    /**
     * Getter for abstract property.
     *
     * @return string
     */
    public function getAbstract();

    /**
     * Prints the object.
     *
     * @return string
     */
    public function __toString();
}
