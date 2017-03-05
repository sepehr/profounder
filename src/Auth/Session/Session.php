<?php

namespace Profounder\Auth\Session;

use Profounder\Foundation\Http\Parser\ParsedObject;

/**
 * Class representing an authenticated session.
 *
 * @property  array $cookie
 */
class Session extends ParsedObject implements SessionContract
{
    /**
     * @inheritdoc
     */
    public function getCookie($key = null)
    {
        return $this->cookie[$key] ?? $this->cookie;
    }
}
