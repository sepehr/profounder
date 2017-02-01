<?php

namespace Profounder\Augment\Command;

use Profounder\Augment\Request;
use Profounder\Augment\Augmentor;
use Profounder\Augment\ArticlePage;
use Profounder\Service\IdentityPool;
use Profounder\Augment\ResponseParser;
use Profounder\Core\ContainerAwareCommand;
use Profounder\Core\Concern\Benchmarkable;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Augment extends ContainerAwareCommand
{
    use Benchmarkable;

    /**
     * Request instance.
     *
     * @var Request
     */
    private $request;

    /**
     * Instance of ResponseParser.
     *
     * @var ResponseParser
     */
    private $parser;

    /**
     * Instance of IdentityPool.
     *
     * @var IdentityPool
     */
    private $identity;

    /**
     * An object of input options.
     *
     * @var object
     */
    private $options;

    /**
     * Identity session object.
     *
     * @var object
     */
    private $session;

    /**
     * Target article's content ID.
     *
     * @var string
     */
    private $articleId;

    /**
     * @var Augmentor
     */
    private $augmentor;

    /**
     * Augment command constructor.
     *
     * @param  Request $request
     * @param  ResponseParser $parser
     * @param  Augmentor $augmentor
     * @param  IdentityPool $identity
     */
    public function __construct(Request $request, ResponseParser $parser, Augmentor $augmentor, IdentityPool $identity)
    {
        $this->request   = $request;
        $this->parser    = $parser;
        $this->augmentor = $augmentor;
        $this->identity  = $identity;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('profounder:augment')
            ->setDescription('Augments article entities with TOC, page length and abstract text.')
            ->addArgument('content-id', InputArgument::OPTIONAL, 'Article content ID.')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Debug mode.')
            ->addOption('id', 'i', InputOption::VALUE_OPTIONAL, 'Process ID.', 1)
            ->addOption('delay', 'w', InputOption::VALUE_OPTIONAL, 'Inter-request delay in milliseconds', null);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->options   = (object)$input->getOptions();
        $this->articleId = $input->getArgument('content-id');
        $this->session   = $this->identity->retrieve(intval($this->options->id - 1));

        $output->writeln("Processing article #{$this->articleId}");

        $this->benchmark(function () use ($output) {
            $articlePage = $this->getArticlePage();
            $output->writeln("Parsed article page in {$this->elapsed()}ms");

            if ($this->options->debug) {
                $output->writeln('<comment>[DEBUG] ' . $articlePage . '</>');
            }

            $this->augmentArticleWith($articlePage)
                ? $output->writeln("Augmented the article entity in {$this->elapsed()}ms")
                : $output->writeln('<error>Could not augment the article entity</>');
        }, 'execution');

        $output->writeln("Total exectution time: {$this->elapsed('execution')}ms");
    }

    /**
     * Augments the article entity with the data from an ArticlePage.
     *
     * @param  ArticlePage $articlePage
     *
     * @return bool
     */
    private function augmentArticleWith(ArticlePage $articlePage)
    {
        return $this->benchmark(function () use ($articlePage) {
            return $this->augmentor->augment($this->articleId, $articlePage);
        });
    }

    /**
     * Fetches the article page and parses it into an ArticlePage.
     *
     * @return ArticlePage
     */
    private function getArticlePage()
    {
        return $this->benchmark(function () {
            return $this->parseResponse($this->fetchArticlePage());
        });
    }

    /**
     * Fetches the article page.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function fetchArticlePage()
    {
        return $this->request
            ->withArticle($this->articleId)
            ->dispatch($this->session->cookie, $this->options->delay);
    }

    /**
     * Parses the response into an array.
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     *
     * @return ArticlePage
     */
    private function parseResponse($response)
    {
        return $this->parser->parse($response);
    }
}
