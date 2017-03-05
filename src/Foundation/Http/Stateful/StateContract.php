<?php
namespace Profounder\Foundation\Http\Stateful;

use Profounder\Foundation\Http\Parser\ParsedObjectContract;

interface StateContract extends ParsedObjectContract
{
    /**
     * Getter for data property.
     *
     * @param string|null $key
     *
     * @return array|string
     */
    public function getData($key = null);

    /**
     * Getter for cookie property.
     *
     * @param string|null $key
     *
     * @return array|string
     */
    public function getCookie($key = null);
}
