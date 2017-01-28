<?php

namespace Profounder\Commands;

use Profounder\Benchmarkable;
use Profounder\Query\ResultStorer;
use Profounder\Query\ResponseParser;
use Profounder\ContainerAwareCommand;
use Profounder\Query\QueryableInputOptions;
use Profounder\Query\Builder as QueryBuilder;
use Profounder\Query\Request as QueryRequest;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Query extends ContainerAwareCommand
{
    use Benchmarkable, QueryableInputOptions;

    /**
     * An object of input options.
     *
     * @var object
     */
    private $options;

    /**
     * An object of identity session.
     *
     * @var object
     */
    private $session;

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
        $this->session = $this->identityPool->retrieve(intval($this->options->id - 1));

        $totalInserts = 0;
        $this->benchmark(function () use ($output, &$totalInserts) {
            for ($i = 1; $i <= $this->options->loop; $i++) {
                $output->writeln("Loop#$i; offset: {$this->options->offset}; limit: {$this->options->limit}");

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
                $this->session->cookies
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
            return ResultStorer::create($this->db)->store($results);
        });
    }

    /**
     * Builds the querystring from the input options.
     *
     * @return string
     */
    private function buildQuery()
    {
        return QueryBuilder::create()
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
     * @param  string $cookies
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function dispatchRequest($query, $cookies)
    {
        return QueryRequest::create($this->http, $query, $cookies)->dispatch();
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
        return ResponseParser::create($response)->parse();
    }

    /**
     * Outputs debug information about the query.
     *
     * @param  OutputInterface $output
     *
     * @return void
     */
    private function outputDebug(OutputInterface $output)
    {
        if ($this->options->debug) {
            $output->writeln('<comment>[DEBUG][QUERY] ' . urldecode($this->buildQuery()) . '</>');
        }
    }
}
