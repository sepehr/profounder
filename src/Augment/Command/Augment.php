<?php

namespace Profounder\Augment\Command;

use Profounder\Augment\ArticlePage;
use Profounder\Augment\RequestContract;
use Profounder\Augment\AugmentorContract;
use Profounder\Core\ContainerAwareCommand;
use Profounder\Core\Concern\Benchmarkable;
use Profounder\Service\IdentityPoolContract;
use Profounder\Augment\ResponseParserContract;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Profounder\Augment\Command\Concern\AugmentableInputOptions;

class Augment extends ContainerAwareCommand
{
    use Benchmarkable, AugmentableInputOptions;

    /**
     * Request instance.
     *
     * @var RequestContract
     */
    private $request;

    /**
     * ResponseParser instance.
     *
     * @var ResponseParserContract
     */
    private $parser;

    /**
     * IdentityPool instance.
     *
     * @var IdentityPoolContract
     */
    private $identity;

    /**
     * Augmentor instance.
     *
     * @var AugmentorContract
     */
    private $augmentor;

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
     * Augment command constructor.
     *
     * @param  RequestContract $request
     * @param  AugmentorContract $augmentor
     * @param  ResponseParserContract $parser
     * @param  IdentityPoolContract $identity
     */
    public function __construct(
        RequestContract $request,
        AugmentorContract $augmentor,
        ResponseParserContract $parser,
        IdentityPoolContract $identity
    ) {
        $this->parser    = $parser;
        $this->request   = $request;
        $this->identity  = $identity;
        $this->augmentor = $augmentor;

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
            ->registerInputOptions();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->options   = (object)$input->getOptions();
        $this->articleId = $input->getArgument('content-id');
        $this->session   = $this->identity->retrieve(intval($this->options->id - 1));

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
            return $this->parseResponse($this->requestArticlePage());
        });
    }

    /**
     * Fetches the article page.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function requestArticlePage()
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
