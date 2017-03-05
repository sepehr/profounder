<?php

namespace Profounder\Foundation\Http\Stateful;

use Profounder\Foundation\Http\Parser\ParsedObject;

/**
 * Class representing a request state data and cookie.
 *
 * @property  array $data
 * @property  array $cookie
 */
class State extends ParsedObject implements StateContract
{
    /**
     * @inheritdoc
     */
    public function getData($key = null)
    {
        return $this->data[$key] ?? $this->data;
    }

    /**
     * @inheritdoc
     */
    public function getCookie($key = null)
    {
        return $this->cookie[$key] ?? $this->cookie;
    }
}
