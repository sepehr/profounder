<?php

namespace Profounder\Core\Console;

use Illuminate\Container\Container as IlluminateContainer;

class Container extends IlluminateContainer
{
    /**
     * Returns application base path.
     *
     * @param  string $path
     *
     * @return string
     */
    public function basePath($path = '')
    {
        return base_path($path);
    }

    /**
     * Returns application database directory path.
     *
     * @return string
     */
    public function databasePath()
    {
        return base_path("database/");
    }

    /**
     * Returns application environment.
     *
     * @return string
     */
    public function environment()
    {
        return $this->config->get('env');
    }
}
