<?php

namespace Profounder\Commands;

use Profounder\Benchmarkable;
use Profounder\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Dumper extends ContainerAwareCommand
{
    use Benchmarkable;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('profounder:dump')
            ->setDescription('Dump articles from database to file.')
            ->addOption('sku', 's', InputOption::VALUE_NONE, 'Plain text SKU dump.')
            ->addArgument('file', InputArgument::REQUIRED, 'File path to dump to.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->outputFiglet($output);

        if (! $count = $this->db->table('articles')->count()) {
            $output->writeln('No results in the database.');
            exit(1);
        }

        $output->writeln("Dumping $count records to the file... It takes a few minutes...");

        $file = $input->getArgument('file');

        $this->benchmark(function () use ($input, $file) {
            $input->getOption('sku')
                ? $this->dumpSku($file)
                : $this->dumpCsv($file);
        });

        $output->writeln("Dumped to file: $file");
        $output->writeln("Execution time: {$this->elapsed()}ms");
    }

    /**
     * Dumps article SKUs to file.
     *
     * @param  string $file File path to dump to.
     *
     * @return void
     */
    private function dumpSku($file)
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

    /**
     * Dumps articles to CSV file.
     *
     * @param  string $file File path to dump to.
     *
     * @return void
     */
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
