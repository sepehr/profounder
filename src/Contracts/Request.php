<?php

namespace Profounder\Contracts;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

interface Request
{
    /**
     * Request initializor.
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
    public function initialize(array $headers = [], array $data = [], $uri = null, $delay = null);

    /**
     * Request dispatcher.
     *
     * @param  string $cookie
     * @param  int|float|null $delay
     *
     * @return ResponseInterface
     */
    public function dispatch($cookie = null, $delay = null);

    /**
     * Sets a data item.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return $this
     */
    public function withData($key, $value);

    /**
     * Sets cookie header.
     *
     * @param  string $cookie
     *
     * @return $this
     */
    public function withCookie($cookie);

    /**
     * Sets User-Agent header.
     *
     * @param  string|null $ua
     *
     * @return $this
     */
    public function withUserAgent($ua = null);

    /**
     * Sets HTTP client instance.
     *
     * @param  ClientInterface $client
     *
     * @return $this
     */
    public function setClient(ClientInterface $client);

    /**
     * Sets request headers.
     *
     * @param  array $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers);

    /**
     * Sets request data.
     *
     * @param  array $data
     *
     * @return $this
     */
    public function setData(array $data);

    /**
     * Sets request target URI.
     *
     * @param  string $uri
     *
     * @return $this
     */
    public function setUri($uri);

    /**
     * Sets request delay.
     *
     * @param  int|float $delay
     *
     * @return $this
     */
    public function setDelay($delay);
}
