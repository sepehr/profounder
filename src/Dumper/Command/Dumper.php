<?php

namespace Profounder\Dumper\Command;

use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Profounder\Core\Concern\Benchmarkable;
use Profounder\Core\Console\ContainerAwareCommand;
use Profounder\Persistence\Entity\ArticleContract;
use Profounder\Persistence\Repository\ArticleRepositoryContract;

class Dumper extends ContainerAwareCommand
{
    use Benchmarkable;

    /**
     * Article repository instance.
     *
     * @var ArticleRepositoryContract
     */
    private $repository;

    /**
     * Filesystem instance.
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Dumper constructor.
     *
     * @param  ArticleRepositoryContract $repository
     * @param  Filesystem $filesystem
     */
    public function __construct(ArticleRepositoryContract $repository, Filesystem $filesystem)
    {
        $this->repository = $repository;
        $this->filesystem = $filesystem;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('profounder:dump')
            ->setDescription('Dumps articles from database to file.')
            ->addArgument('file', InputArgument::REQUIRED, 'File path to dump to.')
            ->addOption('sku', 's', InputOption::VALUE_NONE, 'Plain text SKU dump.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->outputFiglet($output);

        if (! $count = $this->repository->count()) {
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
        $this->filesystem->put($file, $this->repository->dumpSku());
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
        $this->filesystem->put($file, $this->repository->dumpCsv());
    }
}
