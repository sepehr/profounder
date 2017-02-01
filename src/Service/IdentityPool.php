<?php

namespace Profounder\Service;

use Illuminate\Filesystem\Filesystem;
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
     * Filesystem instance.
     *
     * @var Filesystem
     */
    private $files;

    /**
     * Sessions file name.
     *
     * @var string
     */
    private $sessionsFile = 'sessions.json';

    /**
     * IdentityPool constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->files = $filesystem;

        $this->loadFromFile();
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

    /**
     * Loads sessions from file.
     *
     * @return $this
     */
    private function loadFromFile()
    {
        $this->pool = json_decode(
            $this->files->get(storage_path($this->sessionsFile))
        );

        return $this;
    }
}
