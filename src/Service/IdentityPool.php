<?php

namespace Profounder\Service;

use Profounder\Exception\InvalidSession;

class IdentityPool
{
    /**
     * Sessions array of username, password and cookie.
     *
     * @var array
     */
    private $pool;

    /**
     * IdentityPool constructor.
     */
    public function __construct()
    {
        $this->pool = json_decode(file_get_contents(storage_path('sessions.json')));
    }

    /**
     * Returns a session object by ID.
     *
     * @param  int $id
     *
     * @return object
     *
     * @throws InvalidSession
     */
    public function retrieve(int $id)
    {
        if (isset($this->pool[$id])) {
            return (object) $this->pool[$id];
        }

        throw new InvalidSession('Out of identity sessions. Add more sessions or reduce workers.');
    }

    /**
     * Retrieves a random session.
     *
     * @return object
     */
    public function random()
    {
        return $this->retrieve(array_rand($this->pool));
    }
}
