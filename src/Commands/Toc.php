<?php

namespace Profounder\Commands;

use Profounder\Entity\Article;
use Profounder\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Toc extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName('profounder:toc')
            ->setDescription('Collect TOC and abstract text of an article by its content ID.')
            ->addArgument('contentId', InputArgument::REQUIRED, 'Article content ID.')
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'Process ID.', 1);
    }

    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // TODO: Implement...
    }
}
