<?php

namespace Profounder\Augment\Concern;

use Symfony\Component\Console\Input\InputOption;

trait AugmentableInputOptions
{
    /**
     * Registers augment command input options.
     *
     * @return $this
     */
    private function registerInputOptions()
    {
        $this
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Debug mode.')
            ->addOption('id', 'i', InputOption::VALUE_OPTIONAL, 'Process ID.', 1)
            ->addOption('delay', 'w', InputOption::VALUE_OPTIONAL, 'Inter-request delay in milliseconds', null);

        return $this;
    }
}
