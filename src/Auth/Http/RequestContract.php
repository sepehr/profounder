<?php

namespace Profounder\Auth\Http;

use Profounder\Service\Identity\IdentityContract;

interface RequestContract extends \Profounder\Foundation\Http\RequestContract
{
    /**
     * Updates request data based on the passed Identity instance.
     *
     * @param IdentityContract $identity
     *
     * @return RequestContract
     */
    public function actAs(IdentityContract $identity);
}
