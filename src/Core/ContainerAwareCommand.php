<?php

namespace Profounder\Core;

/**
 * @property-read \Illuminate\Log\Writer $log
 * @property-read \Illuminate\Config\Repository $config
 * @property-read \Illuminate\Events\Dispatcher $events
 * @property-read \Illuminate\Filesystem\Filesystem $files
 * @property-read \Illuminate\Database\Capsule\Manager $db
 */
abstract class ContainerAwareCommand extends Command
{
    /**
     * Resolves a resource out of the container.
     *
     * @param  string $abstract
     * @param  array $params
     *
     * @return mixed
     */
    public function make($abstract, array $params = [])
    {
        return $this->getApplication()->getContainer()->make($abstract, $params);
    }

    /**
     * Magic container resource accessor.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        $container = $this->getApplication()->getContainer();

        if ($container->bound($key)) {
            return $container->make($key);
        }
    }
}
