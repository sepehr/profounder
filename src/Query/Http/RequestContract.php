<?php

namespace Profounder\Query\Http;

interface RequestContract extends \Profounder\Foundation\Http\RequestContract
{
    /**
     * Sets "SearchFilter" data parameter.
     *
     * @param string $query
     *
     * @return $this
     */
    public function withQuery($query);
}
