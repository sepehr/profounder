<?php

namespace Profounder\Foundation\Http\Stateful;

use Profounder\Foundation\Http\RequestContract;

interface StatefulRequestContract extends RequestContract
{
    /**
     * Returns internal State instance.
     *
     * @return State
     */
    public function getState();
}
