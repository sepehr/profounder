<?php

namespace Profounder;

use GuzzleHttp\ClientInterface;

abstract class Request implements RequestContract
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
     * @inheritdoc
     */
    public static function create(...$args)
    {
        return new static(...$args);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function dispatch($cookie = null, $delay = null)
    {
        $delay  && $this->setDelay($delay);
        $cookie && $this->withCookie($cookie);

        return $this->client->{$this->method}($this->uri, $this->buildOptions());
    }

    /**
     * @inheritdoc
     */
    public function withData($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function withCookie($cookie)
    {
        $this->headers['Cookie'] = $cookie;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function withUserAgent($ua = null)
    {
        $this->headers['User-Agent'] = $ua ?: $this->randomUserAgent();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @inheritdoc
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
