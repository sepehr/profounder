<?php

namespace Profounder\Service\Identity;

use Illuminate\Filesystem\Filesystem;

class JsonFilePool extends Pool
{
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
     * FilePool constructor.
     *
     * @param  Filesystem $filesystem
     * @param  array $pool
     */
    public function __construct(Filesystem $filesystem, array $pool = null)
    {
        $this->files = $filesystem;

        $pool ? parent::__construct($pool) : $this->loadFromFile();
    }

    /**
     * Fills session pool from file.
     *
     * @return void
     */
    protected function loadFromFile()
    {
        $this->pool = json_decode(
            $this->files->get(storage_path($this->sessionsFile))
        );

        foreach ($this->pool as &$identity) {
            $identity->cookie = $this->normalizeRequestCookies($identity->cookie);
        }
    }

    /**
     * Normalizes request cookie string into an array.
     *
     * @param  string $cookie
     *
     * @return array
     */
    private function normalizeRequestCookies($cookie)
    {
        return array_map('trim', explode(';', $cookie));
    }
}
