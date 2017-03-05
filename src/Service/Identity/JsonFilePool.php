<?php

namespace Profounder\Service\Identity;

use Profounder\Core\StorageContract;

class JsonFilePool extends Pool
{
    /**
     * Storage instance.
     *
     * @var StorageContract
     */
    private $storage;

    /**
     * Sessions file name.
     *
     * @var string
     */
    private $sessionsFile = 'sessions.json';

    /**
     * JsonFilePool constructor.
     *
     * @param StorageContract $storage
     * @param array           $pool
     */
    public function __construct(StorageContract $storage, array $pool = null)
    {
        $this->storage = $storage;

        $pool ? parent::__construct($pool) : $this->loadFromFile();
    }

    /**
     * Fills session pool from file.
     */
    protected function loadFromFile()
    {
        $this->pool = json_decode($this->storage->get($this->sessionsFile));
    }
}
