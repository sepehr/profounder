<?php

namespace Profounder;

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
