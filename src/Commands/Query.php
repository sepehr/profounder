<?php

namespace Profounder\Commands;

use Profounder\ContainerAwareCommand;
use Psr\Http\Message\MessageInterface;
use Profounder\Exceptions\InvalidSession;
use GuzzleHttp\Exception\RequestException;
use Profounder\Exceptions\InvalidQueryResponse;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Query extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('profounder:query')
            ->setDescription('Dispatches a query tp profound.com endpoint and stores the results.')
            ->addOption('offset', 'o', InputOption::VALUE_OPTIONAL, 'Starting offset.', 0)
            ->addOption('chunk', 'c', InputOption::VALUE_OPTIONAL, 'Chunk size.', 5)
            // Accepted: docdatetime, price, mrdclongfalloffextrafresh
            ->addOption('sort', 's', InputOption::VALUE_OPTIONAL, 'Sort by field.', 'docdatetime')
            // Accepted: desc, asc
            ->addOption('order', 'r', InputOption::VALUE_OPTIONAL, 'Sort order.', 'desc')
            // Format: yyyy-mm-dd,yyyy-mm-dd|MAX
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'Comma separated date range.')
            ->addOption('loop', 'l', InputOption::VALUE_OPTIONAL, 'Loop count.', 1)
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'Process ID.', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // HERE'S SOME SPAGHETTI FOR YOU!

        $offset  = $input->getOption('offset');
        $chunk   = $input->getOption('chunk');
        $loop    = $input->getOption('loop');
        $id      = $input->getOption('id');
        $sort    = $input->getOption('sort');
        $order   = $input->getOption('order');
        $range   = $input->getOption('date');
        $session = $this->identityPool->retrieve(intval($id - 1));

        $requestCount = $insertCount = 0;

        $output->writeln("Acting as {$session->username}...");
        $this->watch->start('execution');

        for ($i = 1; $i <= $loop; $i++) {
            $output->writeln("Loop#$i; offset: $offset; chunk: $chunk");

            try {
                $this->watch->start("req#$i");

                $requestCount++;
                $response = $this->http->post('http://www.profound.com/home/FilterSearchResults', [
                    'headers'     => [
                        'Content-Type'     => 'application/x-www-form-urlencoded; charset=UTF-8',
                        'Cookie'           => $session->cookies,
                        'Referer'          => 'http://www.profound.com/home/search',
                        'User-Agent'       => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.98 Safari/537.36',
                        'X-Requested-With' => 'XMLHttpRequest',
                    ],
                    'form_params' => [
                        // Few examples:
                        //&filters=+sitelist,PI,,+boolsalebysliceallowed,1,,+forsale,1
                        //querystring=&offset=0&sortby=mrdclongfalloffextrafresh&sortorder=desc&filters=+sitelist,PI,,+forsale,1&hits=25&searchcontent=(israel )
                        //querystring=&offset=0&hits=25&sortby=mrdclongfalloffextrafresh&sortorder=desc&filters=<>docdatetime,2016-12-22,MAX
                        'SearchFilter'      => "querystring=&offset=$offset&hits=$chunk&sortby=$sort&sortorder=$order&filters=<>docdatetime,$range",
                        'HasUsedNavFilters' => 'false',
                        'searchMethod'      => '/home/FilterSearchResults',
                    ],
                    //'sink' => storage_path('sink.txt'),
                ]);

                $output->writeln("Request#$i time: {$this->watch->stop("req#$i")->getDuration()}ms");

                $json = $this->parseAndValidateJsonResopnse($response);

                if (is_null($json['Results'])) {
                    $output->writeln('No results found, skipping...');
                    continue;
                }

                $resultCount = count($json['Results']);
                $output->writeln("Found <info>$resultCount</> article results...");

                $loopInserts = 0;
                $this->watch->start("db#$i");
                foreach ($json['Results'] as $article) {
                    if ($this->insertIfNotExists($article)) {
                        $loopInserts++;
                        $insertCount++;
                    }
                }

                $output->writeln(
                    "Database#$i: <info>$loopInserts</> new entries in {$this->watch->stop("db#$i")->getDuration()}ms"
                );

                $offset += $chunk;
            } catch (RequestException $e) {
                $this->log->error($e->getMessage());
                $output->writeln('An error has occured: ' . $e->getMessage());
            }
        } // for

        $output->writeln(
            "Performed a total of <info>$requestCount</> requests, gathering a max of " . $chunk * $loop . " articles."
        );
        $output->writeln("Total execution time: <info>{$this->watch->stop('execution')->getDuration()}</>ms");
    }

    /**
     * Parses and validates a Guzzle response.
     *
     * @param  MessageInterface $response
     *
     * @return array
     */
    private function parseAndValidateJsonResopnse(MessageInterface $response)
    {
        $this->validateJsonResponse($json = $this->parseJsonResopnse($response));

        return $json;
    }

    /**
     * Parses a Guzzle response into an array.
     *
     * @param  MessageInterface $response
     *
     * @return array
     *
     * @throws InvalidQueryResponse
     */
    private function parseJsonResopnse(MessageInterface $response)
    {
        $content = (string) $response->getBody();
        if (strpos($content, 'web server encountered a critical error')) {
            throw new InvalidQueryResponse('Remote webserver critical hiccup! damn.');
        }

        $json = json_decode($content, true);
        if (is_null($json)) {
            throw new InvalidQueryResponse('Retrieved invalid JSON response.');
        }

        return $json;
    }

    /**
     * Validates a JSON-decoded response array.
     *
     * @param  array $response
     *
     * @return bool
     *
     * @throws InvalidSession
     * @throws InvalidQueryResponse
     */
    private function validateJsonResponse(array $response)
    {
        if ($response['UserIsLoggedOut']) {
            throw new InvalidSession('Current session has been expired! you need to feed me some new sessions.');
        }

        if (! empty($response['ErrorMessage'])) {
            throw new InvalidQueryResponse("Remote error: {$response['ErrorMessage']}");
        }

        return true;
    }

    /**
     * Inserts a new article record into the database if necessary.
     *
     * @param  array $article
     *
     * @return bool
     */
    private function insertIfNotExists(array $article)
    {
        if ($this->db->table('articles')->where(['internal_id' => $article['InternalId']])->exists()) {
            return false;
        }

        return $this->db->table('articles')->insert([
            'internal_id' => $article['InternalId'],
            'content_id'  => $article['ContentId'],
            'title'       => $article['Title'],
            'date'        => date('Y-m-d H:i:s', strtotime($article['DocDateTime'])),
            'publisher'   => $article['Publisher'],
            'sku'         => $article['Sku'],
            'price'       => intval(preg_replace('/([^0-9\\.])/i', '', $article['Price']) * 100),
        ]);
    }
}
