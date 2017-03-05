<?php

namespace Profounder\Augment\Http\Parser;

interface ParserContract extends \Profounder\Foundation\Http\Parser\ParserContract
{
    /**
     * Sets defaults property.
     *
     * @param array $defaults
     *
     * @return $this
     */
    public function withDefaults(array $defaults);
}
