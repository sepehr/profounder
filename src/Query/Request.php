<?php

namespace Profounder\Query;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class Request
{
    /**
     * HTTP client instance.
     *
     * @var ClientInterface
     */
    private $client;

    /**
     * Request target URI.
     *
     * @var string
     */
    private $uri;

    /**
     * Request data.
     *
     * @var array
     */
    private $data = [];

    /**
     * Request headers.
     *
     * @var array
     */
    private $headers = [];

    /**
     * Request constructor.
     *
     * @param  ClientInterface $client
     * @param  string $query Search query string.
     * @param  string $cookies Cookie header string.
     */
    public function __construct(ClientInterface $client, $query, $cookies)
    {
        $this->initialize($client, ['Cookie' => $cookies], ['SearchFilter' => $query]);
    }

    /**
     * Static factory method.
     *
     * @param  ClientInterface $client
     * @param  string $query Search query string.
     * @param  string $cookies Cookie header string.
     *
     * @return Request
     */
    public static function create(ClientInterface $client, $query, $cookies)
    {
        return new static($client, $query, $cookies);
    }

    /**
     * Class initializor.
     *
     * Knows how to init a request to profound.com's search endpoint with proper defaults.
     *
     * @param  ClientInterface $client
     * @param  array $headers
     * @param  array $data
     * @param  string|null $uri
     *
     * @return Request
     */
    public function initialize(ClientInterface $client, array $headers = [], array $data = [], $uri = null)
    {
        $this->setClient($client);

        $this->setHeaders(array_replace([
            'Cookie'           => '',
            'X-Requested-With' => 'XMLHttpRequest',
            'User-Agent'       => $this->randomUserAgent(),
            'Referer'          => 'http://www.profound.com/home/search',
            'Content-Type'     => 'application/x-www-form-urlencoded; charset=UTF-8',
        ], $headers));

        $this->setData(array_replace([
            'SearchFilter'      => '',
            'HasUsedNavFilters' => 'false',
            'searchMethod'      => '/home/FilterSearchResults',
        ], $data));

        $this->setUri($uri ?: 'http://www.profound.com/home/FilterSearchResults');

        return $this;
    }

    /**
     * Request dispatcher.
     *
     * @return ResponseInterface
     */
    public function dispatch()
    {
        // Always a POST request
        return $this->client->post($this->uri, [
            'form_params' => $this->data,
            'headers'     => $this->headers,
        ]);
    }

    /**
     * Sets HTTP client instance.
     *
     * @param  ClientInterface $client
     *
     * @return Request
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Sets request headers.
     *
     * @param  array $headers
     *
     * @return Request
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Sets request data.
     *
     * @param  array $data
     *
     * @return Request
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Sets request target URI.
     *
     * @param  string $uri
     *
     * @return Request
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Returns a random valid UA string.
     *
     * @return string
     */
    private function randomUserAgent()
    {
        $uas = [
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_2) AppleWebKit/537.36 (KHTML, like Gecko) ' .
            'Chrome/54.0.2840.98 Safari/537.36',
            // Add more user agents here, if needed...
        ];

        return $uas[array_rand($uas)];
    }
}
