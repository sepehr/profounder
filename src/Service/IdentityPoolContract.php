<?php
namespace Profounder\Service;

use Profounder\Exception\InvalidSession;

interface IdentityPoolContract
{
    /**
     * Returns a session object by ID.
     *
     * @param  int $id
     *
     * @return object
     *
     * @throws InvalidSession
     */
    public function retrieve($id);

    /**
     * Retrieves a random session.
     *
     * @return object
     */
    public function random();
}
