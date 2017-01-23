<?php

namespace Profounder\Commands;

use Profounder\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SkuDumper extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('profounder:sku')
            ->setDescription('Dump article SKUs from database to file.')
            ->addArgument('file', InputArgument::REQUIRED, 'File path to dump to.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Dumping to the file... It may take a few minutes...');

        $start = microtime(true);
        $file  = $input->getArgument('file');

        $this->db
            ->table('articles')
            ->select('sku')
            ->chunk(1000, function ($skus) use ($file) {
                $skus = $skus->reduce(function ($carry, $item) {
                    return $carry .= $item->sku . PHP_EOL;
                }, '');

                $this->files->append($file, $skus);
            });

        $output->writeln("Dumped all skus to file:\n$file\nExecution time: " . (microtime(true) - $start));
    }
}
