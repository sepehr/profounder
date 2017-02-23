<?php

namespace Profounder;

interface StatefulRequestContract extends RequestContract
{
    /**
     * Returns internal State instance.
     *
     * @return State
     */
    public function getState();
}
