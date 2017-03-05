<?php

namespace Profounder\Foundation\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJarInterface;

interface RequestContract
{
    /**
     * Static factory method.
     *
     * @param array $args
     *
     * @return Request
     */
    public static function create(...$args);

    /**
     * Request initializor.
     *
     * Knows how to init a request to profound.com's search endpoint with proper defaults.
     *
     * @param array          $headers
     * @param array          $data
     * @param string|null    $uri
     * @param int|float|null $delay
     *
     * @return $this
     */
    public function initialize(array $headers = [], array $data = [], $uri = null, $delay = null);

    /**
     * Request dispatcher.
     *
     * @param int|float|null    $delay
     * @param array|string|null $cookie
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function dispatch($delay = null, $cookie = null);

    /**
     * Sets a single data item, or an array of them.
     *
     * @param string|array $key
     * @param mixed|null   $value
     *
     * @return $this
     */
    public function withData($key, $value = null);

    /**
     * Sets a single header, or an array of them.
     *
     * @param string|array $key
     * @param mixed|null   $value
     *
     * @return $this
     */
    public function withHeader($key, $value = null);

    /**
     * Sets request cookie(s).
     *
     * @param array|string|null $cookie
     *
     * @return $this
     */
    public function withCookie($cookie);

    /**
     * Sets User-Agent header.
     *
     * @param string|null $ua
     *
     * @return $this
     */
    public function withUserAgent($ua = null);

    /**
     * Sets HTTP client instance.
     *
     * @param ClientInterface $client
     *
     * @return $this
     */
    public function setClient(ClientInterface $client);

    /**
     * Sets CookieJar instance.
     *
     * @param CookieJarInterface $cookieJar
     *
     * @return $this
     */
    public function setCookieJar(CookieJarInterface $cookieJar);

    /**
     * Sets request headers.
     *
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers);

    /**
     * Sets request data.
     *
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data);

    /**
     * Sets request target URI.
     *
     * @param string $uri
     *
     * @return $this
     */
    public function setUri($uri);

    /**
     * Sets request delay.
     *
     * @param int|float $delay
     *
     * @return $this
     */
    public function setDelay($delay);
}
