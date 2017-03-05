<?php

namespace Profounder\Augment\Command;

use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Profounder\Core\Concern\Benchmarkable;
use Profounder\Core\Console\ContainerAwareCommand;
use Profounder\Augment\Command\Concern\AugmentableInputOptions;
use Profounder\Persistence\Entity\ArticleContract;
use Profounder\Persistence\Repository\ArticleRepositoryContract;

class MassAugment extends ContainerAwareCommand
{
    use Benchmarkable, AugmentableInputOptions;

    /**
     * Article repository instance.
     *
     * @var ArticleRepositoryContract
     */
    private $repository;

    /**
     * Augment command name.
     *
     * @var string
     */
    private $augmentCommandName = 'profounder:augment';

    /**
     * BulkAugment constructor.
     *
     * @param  ArticleRepositoryContract  $repository
     */
    public function __construct(ArticleRepositoryContract $repository)
    {
        $this->repository = $repository;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('profounder:augment:mass')
            ->setDescription('Augments existing articles in the database.')
            ->addOption('chunk', 'c', InputOption::VALUE_REQUIRED, 'Query chunk.', 5)
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Query limit.', 10)
            ->addOption('offset', 'o', InputOption::VALUE_REQUIRED, 'Query offset.', 0)
            ->registerInputOptions();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->outputFiglet($output);

        $count   = 0;
        $options = $input->getOptions();
        $command = $this->getApplication()->find($this->augmentCommandName);

        $commandInput = [
            'command'  => $this->augmentCommandName,
            '--id'     => $options['id'],
            '--debug'  => $options['debug'],
            '--delay'  => $options['delay'],
        ];

        // Query builder's chunk() does not respect the set offset and limit,
        // so as a workaround, we use minimum and maximum IDs. See:
        // https://github.com/laravel/internals/issues/103
        $minId = $this->repository->getNonAugmentedIdByOffset($options['offset']);
        $maxId = $this->repository->getNonAugmentedIdByOffset($options['offset'] + $options['limit'] - 1);

        $this->repository->executeOnNonAugmentedWithin(
            $minId,
            $maxId,
            $options['chunk'],
            function (Collection $articles) use ($command, $commandInput, $output, &$count) {
                $articles->each(function (ArticleContract $article) use ($command, $commandInput, $output, &$count) {
                    $commandInput['content-id'] = $article->getContentId();

                    $output->writeln("\n>> Processing article #{$article->getId()} ({$article->getContentId()})");

                    $command->run($this->make(ArrayInput::class, $commandInput), $output) or $count++;
                });
            }
        );

        $output->writeln("\nDone! Augmented $count articles.");
    }
}
