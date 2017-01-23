<?php

namespace App;

use Illuminate\Contracts\Container\Container;

/**
 * @property \Illuminate\Log\Writer $log
 * @property \Illuminate\Config\Repository $config
 * @property \Illuminate\Events\Dispatcher $events
 * @property \Illuminate\Filesystem\Filesystem $files
 * @property \Illuminate\Database\Capsule\Manager $db
 */
abstract class ContainerAwareCommand extends Command
{
    /**
     * Container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * ContainerAwareCommand constructor.
     *
     * @param  Container $container
     * @param  string|null $name
     */
    public function __construct(Container $container, $name = null)
    {
        $this->setContainer($container);

        parent::__construct($name);
    }

    /**
     * Sets the internal container instance.
     *
     * @param  Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the container.
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Container property accessor.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        $container = $this->getContainer();

        if ($container->bound($key)) {
            return $container->make($key);
        }
    }
}
