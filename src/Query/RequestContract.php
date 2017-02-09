<?php

namespace Profounder\Query;

interface RequestContract extends \Profounder\RequestContract
{
    /**
     * Sets "SearchFilter" data parameter.
     *
     * @param  string $query
     *
     * @return $this
     */
    public function withQuery($query);
}
