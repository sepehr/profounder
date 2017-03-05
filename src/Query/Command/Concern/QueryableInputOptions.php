<?php

namespace Profounder\Query\Command\Concern;

use Symfony\Component\Console\Input\InputOption;
use Profounder\Query\Http\Builder\Builder as QueryBuilder;

trait QueryableInputOptions
{
    /**
     * Registers query command input options.
     *
     * @param bool $addDateOption
     *
     * @return $this
     */
    private function registerInputOptions($addDateOption = true)
    {
        $this
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Debug mode.')
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'Process ID.', 1)
            ->addOption('loop', 'l', InputOption::VALUE_OPTIONAL, 'Loop count.', 1)
            ->addOption('order', 'r', InputOption::VALUE_OPTIONAL, 'Sort order.', 'desc')
            ->addOption('limit', 'c', InputOption::VALUE_OPTIONAL, 'Chunk limit size.', 5)
            ->addOption('offset', 'o', InputOption::VALUE_OPTIONAL, 'Starting offset.', 0)
            ->addOption('keyword', 'k', InputOption::VALUE_OPTIONAL, 'Search keyword.', '')
            ->addOption('sort', 't', InputOption::VALUE_OPTIONAL, 'Sort by field.', QueryBuilder::DATE)
            ->addOption('delay', 'w', InputOption::VALUE_OPTIONAL, 'Inter-request delay in milliseconds', null);

        if ($addDateOption) {
            $this->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'Comma separated date range.');
        }

        return $this;
    }

    /**
     * Registers all query options but --date.
     *
     * @return $this
     */
    private function registerInputOptionsWithoutDate()
    {
        return $this->registerInputOptions(false);
    }
}
