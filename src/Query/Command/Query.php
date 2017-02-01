<?php

namespace Profounder\Query\Command;

use Profounder\Query\ResultStorer;
use Profounder\Service\IdentityPool;
use Profounder\Query\ResponseParser;
use Profounder\Core\ContainerAwareCommand;
use Profounder\Core\Concern\Benchmarkable;
use Profounder\Query\Builder as QueryBuilder;
use Profounder\Query\Request as QueryRequest;
use Profounder\Query\Concern\QueryableInputOptions;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Query extends ContainerAwareCommand
{
    use Benchmarkable, QueryableInputOptions;

    /**
     * IdentityPool instance.
     *
     * @var IdentityPool
     */
    private $identity;

    /**
     * ResultStorer instance.
     *
     * @var ResultStorer
     */
    private $storer;

    /**
     * QueryBuilder instance.
     *
     * @var QueryBuilder
     */
    private $builder;

    /**
     * QueryRequest instance.
     *
     * @var QueryRequest
     */
    private $request;

    /**
     * ResponseParser instance.
     *
     * @var ResponseParser
     */
    private $parser;

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
     * Query constructor.
     *
     * @param  ResultStorer $storer
     * @param  QueryBuilder $builder
     * @param  QueryRequest $request
     * @param  IdentityPool $identity
     * @param  ResponseParser $parser
     */
    public function __construct(
        ResultStorer $storer,
        QueryBuilder $builder,
        QueryRequest $request,
        IdentityPool $identity,
        ResponseParser $parser
    ) {
        $this->storer   = $storer;
        $this->builder  = $builder;
        $this->request  = $request;
        $this->identity = $identity;
        $this->parser   = $parser;

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
            ->registerQueryInputOptions();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->options = (object) $input->getOptions();
        $this->session = $this->identity->retrieve(intval($this->options->id - 1));

        $totalInserts = 0;
        $this->benchmark(function () use ($output, &$totalInserts) {
            for ($i = 1; $i <= $this->options->loop; $i++) {
                $output->writeln(
                    "Loop=$i; offset={$this->options->offset}; limit={$this->options->limit}; " .
                    ($this->options->delay ? "delay={$this->options->delay}ms" : '')
                );

                $results = $this->query();
                $output->writeln('Fetched <info>' . count($results) . "</> articles in {$this->elapsed()}ms");

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
     * @return array
     */
    private function query()
    {
        return $this->benchmark(function () {
            $response = $this->dispatchRequest(
                $this->buildQuery(),
                $this->session->cookie,
                $this->options->delay
            );

            return $this->parseResponse($response);
        });
    }

    /**
     * Stores the query results.
     *
     * @param  array $results
     *
     * @return int
     */
    private function store($results)
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
     * @param  string $query
     * @param  string $cookie
     * @param  int|float $delay
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function dispatchRequest($query, $cookie, $delay = null)
    {
        return $this->request->withQuery($query)->dispatch($cookie, $delay);
    }

    /**
     * Parses the response into an array.
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     *
     * @return array
     */
    private function parseResponse($response)
    {
        return $this->parser->parse($response);
    }

    /**
     * Outputs debug information about the query.
     *
     * @param  OutputInterface $output
     * @param  array $results
     *
     * @return void
     */
    private function outputDebug(OutputInterface $output, $results = [])
    {
        if ($this->options->debug) {
            $output->writeln('<comment>[DEBUG][QUERY]: ' . urldecode($this->buildQuery()) . '</>');

            if (! empty($results)) {
                $output->writeln("<comment>[DEBUG][RESULTS]:\n" . var_export($results, true) . '</>');
            }
        }
    }
}