<?php

namespace Profounder;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

abstract class Request
{
    /**
     * HTTP client instance.
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * Request target URI.
     *
     * @var string
     */
    protected $uri;

    /**
     * Request method.
     *
     * Supported methods: "post", "get", "put", "delete".
     *
     * @var string
     */
    protected $method;

    /**
     * Delay between requests.
     *
     * @var int|float
     */
    protected $delay;

    /**
     * Whether to throw HTTP errors or not.
     *
     * @var bool
     */
    protected $httpErrors = false;

    /**
     * Request data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Request headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Request constructor.
     *
     * @param  ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->setClient($client)->initialize();
    }

    /**
     * Static factory method.
     *
     * @param  array $args
     *
     * @return Request
     */
    public static function create(...$args)
    {
        return new static(...$args);
    }

    /**
     * Class initializor.
     *
     * Knows how to init a request to profound.com's search endpoint with proper defaults.
     *
     * @param  array $headers
     * @param  array $data
     * @param  string|null $uri
     * @param  int|float|null $delay
     *
     * @return $this
     */
    public function initialize(array $headers = [], array $data = [], $uri = null, $delay = null)
    {
        $this->withUserAgent($this->randomUserAgent());
        $this->setHeaders(array_replace($this->headers, $headers));

        $this->setData(array_replace($this->data, $data));

        $uri && $this->setUri($uri);

        return $this->setDelay($delay);
    }

    /**
     * Request dispatcher.
     *
     * @param  string $cookie
     * @param  int|float|null $delay
     *
     * @return ResponseInterface
     */
    public function dispatch($cookie = null, $delay = null)
    {
        $delay  && $this->setDelay($delay);
        $cookie && $this->withCookie($cookie);

        return $this->client->{$this->method}($this->uri, $this->buildOptions());
    }

    /**
     * Sets a data item.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return $this
     */
    public function withData($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Sets cookie header.
     *
     * @param  string $cookie
     *
     * @return $this
     */
    public function withCookie($cookie)
    {
        $this->headers['Cookie'] = $cookie;

        return $this;
    }

    /**
     * Sets User-Agent header.
     *
     * @param  string|null $ua
     *
     * @return $this
     */
    public function withUserAgent($ua = null)
    {
        $this->headers['User-Agent'] = $ua ?: $this->randomUserAgent();

        return $this;
    }

    /**
     * Sets HTTP client instance.
     *
     * @param  ClientInterface $client
     *
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Sets request delay.
     *
     * @param  int|float $delay
     *
     * @return $this
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Builds and returs an array of request options.
     *
     * @return array
     */
    protected function buildOptions()
    {
        $dataKey = $this->method == 'get' ? 'query' : 'form_params';

        return [
            $dataKey      => $this->data,
            'delay'       => $this->delay,
            'headers'     => $this->headers,
            'http_errors' => $this->httpErrors,
        ];
    }

    /**
     * Returns a random valid UA string.
     *
     * @return string
     */
    protected function randomUserAgent()
    {
        $uas = [
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_2) AppleWebKit/537.36 (KHTML, like Gecko) ' .
            'Chrome/54.0.2840.98 Safari/537.36',
            // Add more user agents here, if needed...
        ];

        return $uas[array_rand($uas)];
    }
}
