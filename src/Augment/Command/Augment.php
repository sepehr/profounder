<?php

namespace Profounder\Augment\Command;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Profounder\Core\Concern\Benchmarkable;
use Profounder\Core\Console\ContainerAwareCommand;
use Profounder\Augment\Http\RequestContract;
use Profounder\Augment\Http\Parser\ParserContract;
use Profounder\Augment\Http\Parser\ArticlePageContract;
use Profounder\Augment\Augmentor\AugmentorContract;
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
     * Parser instance.
     *
     * @var ParserContract
     */
    private $parser;

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
     * Target article's content ID.
     *
     * @var string
     */
    private $articleContentId;

    /**
     * Augment command constructor.
     *
     * @param  ParserContract $parser
     * @param  RequestContract $request
     * @param  AugmentorContract $augmentor
     */
    public function __construct(ParserContract $parser, RequestContract $request, AugmentorContract $augmentor)
    {
        $this->parser    = $parser;
        $this->request   = $request;
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
        $this->options   = (object) $input->getOptions();
        $this->articleContentId = $input->getArgument('content-id');

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
     * @param  ArticlePageContract  $articlePage
     *
     * @return bool
     */
    private function augmentArticleWith(ArticlePageContract $articlePage)
    {
        return $this->benchmark(function () use ($articlePage) {
            return $this->augmentor->augment($this->articleContentId, $articlePage);
        });
    }

    /**
     * Fetches the article page and parses it into an ArticlePage.
     *
     * @return ArticlePageContract
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
            ->withArticle($this->articleContentId)
            ->dispatch($this->options->delay);
    }

    /**
     * Parses the response into an array.
     *
     * @param  ResponseInterface $response
     *
     * @return ArticlePageContract
     */
    private function parseResponse(ResponseInterface $response)
    {
        return $this->parser->parse($response);
    }
}
