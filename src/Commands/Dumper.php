<?php

namespace Profounder\Commands;

use Profounder\Benchmarkable;
use Profounder\ContainerAwareCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Query\Builder;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Dumper extends ContainerAwareCommand
{
    use Benchmarkable;

    /**
     * Query builder instance.
     *
     * @var Builder
     */
    private $queryBuilder;

    /**
     * Filesystem instance.
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Dumper constructor.
     *
     * @param  Capsule $capsule
     * @param  Filesystem $filesystem
     */
    public function __construct(Capsule $capsule, Filesystem $filesystem)
    {
        $this->filesystem   = $filesystem;
        $this->queryBuilder = $capsule->table('articles');

        parent::__construct(null);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('profounder:dump')
            ->setDescription('Dump articles from database to file.')
            ->addArgument('file', InputArgument::REQUIRED, 'File path to dump to.')
            ->addOption('sku', 's', InputOption::VALUE_NONE, 'Plain text SKU dump.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->outputFiglet($output);

        if (! $count = $this->queryBuilder->count()) {
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
        $this->filesystem->put($file, '');

        $this->queryBuilder
            ->select('sku')
            ->chunk(1000, function ($skus) use ($file) {
                $skus = $skus->reduce(function ($carry, $item) {
                    return $carry .= $item->sku . PHP_EOL;
                }, '');

                $this->filesystem->append($file, $skus);
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
        $this->filesystem->put($file, 'ContentID,Title,Date,Price,Publisher,SKU' . PHP_EOL);

        $this->queryBuilder
            ->select('content_id', 'title', 'date', 'price', 'publisher', 'sku')
            ->chunk(1000, function ($articles) use ($file) {
                $articles = $articles->reduce(function ($carry, $item) {
                    return $carry .= '"' . implode('","', (array) $item) . '"' . PHP_EOL;
                }, '');

                $this->filesystem->append($file, $articles);
            });
    }
}
