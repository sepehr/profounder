<?php

namespace Profounder\Query\Command;

use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Profounder\Core\Concern\Benchmarkable;
use Profounder\Core\Console\ContainerAwareCommand;
use Profounder\Query\Http\RequestContract;
use Profounder\Query\Http\Parser\ParserContract;
use Profounder\Query\Http\Builder\BuilderContract;
use Profounder\Query\Storer\StorerContract;

use Profounder\Query\Command\Concern\QueryableInputOptions;

class Query extends ContainerAwareCommand
{
    use Benchmarkable, QueryableInputOptions;

    /**
     * Storer instance.
     *
     * @var StorerContract
     */
    private $storer;

    /**
     * Builder instance.
     *
     * @var BuilderContract
     */
    private $builder;

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
     * An object of input options.
     *
     * @var object
     */
    private $options;

    /**
     * Query constructor.
     *
     * @param StorerContract  $storer
     * @param ParserContract  $parser
     * @param BuilderContract $builder
     * @param RequestContract $request
     */
    public function __construct(
        StorerContract $storer,
        ParserContract $parser,
        BuilderContract $builder,
        RequestContract $request
    ) {
        $this->storer   = $storer;
        $this->parser   = $parser;
        $this->builder  = $builder;
        $this->request  = $request;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('profounder:query')
            ->setDescription('Dispatches a query to profound.com search endpoint and stores the results.')
            ->registerInputOptions();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->options = (object) $input->getOptions();

        $totalInserts = 0;
        $this->benchmark(function () use ($output, &$totalInserts) {
            for ($i = 1; $i <= $this->options->loop; $i++) {
                $output->writeln(
                    "Loop=$i; offset={$this->options->offset}; limit={$this->options->limit}; " .
                    ($this->options->delay ? "delay={$this->options->delay}ms" : '')
                );

                $results = $this->query();
                $output->writeln("Fetched <info>{$results->count()}</> articles in {$this->elapsed()}ms");

                $this->outputDebug($output);

                $totalInserts += $inserts = $this->store($results);
                $output->writeln("Stored <info>$inserts</> new articles in {$this->elapsed()}ms");

                $this->options->offset += $this->options->limit;
            }
        }, 'execution');

        $output->writeln(
            "Collected <info>{$totalInserts}</> new articles within {$this->options->loop} loop(s) ".
            "in {$this->elapsed('execution')}ms"
        );
    }

    /**
     * Queries the remote search endpoint.
     *
     * @return Collection
     */
    private function query()
    {
        return $this->benchmark(function () {
            return $this->parseResponse(
                $this->dispatchRequest($this->buildQuery(), $this->options->delay)
            );
        });
    }

    /**
     * Stores the query results.
     *
     * @param Collection $results
     *
     * @return int
     */
    private function store(Collection $results)
    {
        return $this->benchmark(function () use ($results) {
            return $this->storer->store($results);
        });
    }

    /**
     * Builds the querystring from the input options.
     *
     * @return string
     */
    private function buildQuery()
    {
        return $this->builder
            ->searchFor($this->options->keyword)
            ->byDateString($this->options->date)
            ->orderBy($this->options->sort, $this->options->order)
            ->offset($this->options->offset)
            ->take($this->options->limit)
            ->build();
    }

    /**
     * Dispatches the query request.
     *
     * @param string    $query
     * @param int|float $delay
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function dispatchRequest($query, $delay = null)
    {
        return $this->request->withQuery($query)->dispatch($delay);
    }

    /**
     * Parses the response into a collection of CollectedArticle instances.
     *
     * @param ResponseInterface $response
     *
     * @return Collection
     */
    private function parseResponse(ResponseInterface $response)
    {
        return $this->parser->parse($response);
    }

    /**
     * Outputs debug information about the query.
     *
     * @param OutputInterface $output
     * @param Collection|null $results
     */
    private function outputDebug(OutputInterface $output, Collection $results = null)
    {
        if ($this->options->debug) {
            $output->writeln('<comment>[DEBUG][QUERY]: ' . urldecode($this->buildQuery()) . '</>');

            if ($results) {
                $output->writeln("<comment>[DEBUG][RESULTS]:\n" . var_export($results, true) . '</>');
            }
        }
    }
}
