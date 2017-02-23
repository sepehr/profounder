<?php

namespace Profounder\Dumper\Command;

use Profounder\Entity\Article;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Profounder\Core\ContainerAwareCommand;
use Profounder\Core\Concern\Benchmarkable;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Dumper extends ContainerAwareCommand
{
    use Benchmarkable;

    /**
     * Article repository instance.
     *
     * @var Article
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
     * @param  Article $repository
     * @param  Filesystem $filesystem
     */
    public function __construct(Article $repository, Filesystem $filesystem)
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
        $this->filesystem->put($file, '');

        $this
            ->repository
            ->select('sku')
            ->chunk(1000, function (Collection $skus) use ($file) {
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
        $this->filesystem->put($file, 'ContentID,Title,Date,Price,SKU,Length' . PHP_EOL);

        $this
            ->repository
            ->select('content_id', 'title', 'date', 'price', 'sku', 'length')
            ->chunk(1000, function (Collection $articles) use ($file) {
                $articles = $articles->reduce(function ($carry, $item) {
                    return $carry .= '"' . implode('","', $item->toArray()) . '"' . PHP_EOL;
                }, '');

                $this->filesystem->append($file, $articles);
            });
    }
}
