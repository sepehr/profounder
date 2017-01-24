<?php

namespace Profounder;

use Illuminate\Contracts\Container\Container;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    /**
     * Container instance.
     *
     * @var Container
     */
    private $container;

    /**
     * Application constructor.
     *
     * @param  Container $container
     * @param  string $name
     * @param  string $version
     */
    public function __construct(Container $container, $name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        $this->setContainer($container);

        parent::__construct($name, $version);
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
     * @inheritdoc
     */
    public function renderException(\Exception $e, OutputInterface $output)
    {
        $this->getContainer()->log->error($e);

        parent::renderException($e, $output);
    }
}
