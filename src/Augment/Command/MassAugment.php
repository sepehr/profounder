<?php

namespace Profounder\Augment\Command;

use Profounder\Entity\Article;
use Profounder\Core\ContainerAwareCommand;
use Profounder\Core\Concern\Benchmarkable;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Profounder\Augment\Command\Concern\AugmentableInputOptions;

class MassAugment extends ContainerAwareCommand
{
    use Benchmarkable, AugmentableInputOptions;

    /**
     * Article repository instance.
     *
     * @var Article
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
     * @param  Article $repository
     */
    public function __construct(Article $repository)
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
        $minId = $this->getArticleId($options['offset'] - 1);
        $maxId = $this->getArticleId($options['offset'] + $options['limit']);

        $this
            ->repository
            ->select('id', 'content_id')
            ->whereNull('length')
            ->whereBetween('id', [$minId, $maxId])
            ->orderBy('id')
            ->chunk($options['chunk'], function ($articles) use ($command, $commandInput, $output, &$count) {
                $articles->each(function ($article) use ($command, $commandInput, $output, &$count) {
                    $commandInput['content-id'] = $article->content_id;

                    $output->writeln("\n>> Processing article #{$article->id} ({$article->content_id})");

                    $command->run($this->make(ArrayInput::class, $commandInput), $output) or $count++;
                });
            });

        $output->writeln("\nDone! Augmented $count articles.");
    }

    /**
     * Returns article ID based on passed limit.
     *
     * @param  int $limit
     *
     * @return int
     */
    private function getArticleId($limit)
    {
        return $this
            ->repository
            ->whereNull('length')
            ->orderBy('id')
            ->skip($limit)
            ->take(1)
            ->value('id');
    }
}
