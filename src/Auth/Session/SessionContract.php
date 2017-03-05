<?php

namespace Profounder\Auth\Session;

use Profounder\Foundation\Http\Parser\ParsedObjectContract;

interface SessionContract extends ParsedObjectContract
{
    /**
     * Getter for cookie property.
     *
     * @param null $key
     *
     * @return array|string
     */
    public function getCookie($key = null);
}
