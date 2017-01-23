<?php

namespace Profounder\Commands;

use Profounder\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Dumper extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('profounder:dump')
            ->setDescription('Dump articles from database to file.')
            ->addOption('sku', 's', InputOption::VALUE_NONE, 'Plain text SKU dump.')
            ->addArgument('file', InputArgument::REQUIRED, 'File path to dump to.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (! $count = $this->db->table('articles')->count()) {
            $output->writeln('No results in the database.');
            exit(1);
        }

        $output->writeln("Dumping $count records to the file... It takes a few minutes...");

        $start = microtime(true);
        $file  = $input->getArgument('file');

        $input->getOption('sku')
            ? $this->dumpSkus($file)
            : $this->dumpCsv($file);

        $output->writeln("Dumped to file:\n$file\nExecution time: " . (microtime(true) - $start));
    }

    private function dumpSkus($file)
    {
        $this->files->put($file, '');

        $this->db
            ->table('articles')
            ->select('sku')
            ->chunk(1000, function ($skus) use ($file) {
                $skus = $skus->reduce(function ($carry, $item) {
                    return $carry .= $item->sku . PHP_EOL;
                }, '');

                $this->files->append($file, $skus);
            });
    }

    private function dumpCsv($file)
    {
        $this->files->put($file, 'ContentID,Title,Date,Price,Publisher,SKU' . PHP_EOL);

        $this->db
            ->table('articles')
            ->select('content_id', 'title', 'date', 'price', 'publisher', 'sku')
            ->chunk(1000, function ($articles) use ($file) {
                $articles = $articles->reduce(function ($carry, $item) {
                    return $carry .= '"' . implode('","', (array) $item) . '"' . PHP_EOL;
                }, '');

                $this->files->append($file, $articles);
            });
    }
}
