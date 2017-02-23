<?php

namespace Profounder\Auth\Http;

use Profounder\Service\Identity\Identity;

interface RequestContract extends \Profounder\RequestContract
{
    /**
     * Updates request data based on the passed Identity instance.
     *
     * @param  Identity $identity
     *
     * @return RequestContract
     */
    public function actAs(Identity $identity);
}
