<?php

namespace Profounder;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Cookie\CookieJarInterface;
use Psr\Http\Message\ResponseInterface;

abstract class Request implements RequestContract
{
    /**
     * HTTP client instance.
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     * CookieJarInterface instance.
     *
     * @var CookieJarInterface
     */
    protected $cookieJar;

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
     * Default request options.
     *
     * @var array
     */
    protected $requestOptions = [];

    /**
     * Cookie domain.
     *
     * @var string
     */
    protected $cookieDomain = 'www.profound.com';

    /**
     * Request constructor.
     *
     * @param  ClientInterface $client
     * @param  CookieJarInterface $cookieJar
     */
    public function __construct(ClientInterface $client, CookieJarInterface $cookieJar)
    {
        $this->setClient($client)->setCookieJar($cookieJar)->initialize();
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
        $this->withUserAgent();
        $this->withData($data);
        $this->withHeader($headers);

        $uri && $this->setUri($uri);

        return $this->setDelay($delay);
    }

    /**
     * @inheritdoc
     */
    public function dispatch($delay = null, $cookie = null)
    {
        $delay  and $this->setDelay($delay);
        $cookie and $this->withCookie($cookie);

        $response = $this->dispatchRequest($this->method, $this->uri, $this->buildOptions());

        return $this->alterResponse($response);
    }

    /**
     * @inheritdoc
     */
    public function withData($key, $value = null)
    {
        if (is_array($key)) {
            return $this->setData(array_replace($this->data, $key));
        }

        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function withHeader($key, $value = null)
    {
        if (is_array($key)) {
            return $this->setHeaders(array_replace($this->headers, $key));
        }

        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function withUserAgent($ua = null)
    {
        return $this->withHeader('User-Agent', $ua ?: $this->randomUserAgent());
    }

    /**
     * @inheritdoc
     */
    public function withCookie($cookie)
    {
        if (! is_null($cookie)) {
            is_array($cookie) or $cookie = [$cookie];

            foreach ($cookie as $cookieString) {
                $cookieString = $this->ensureCookieDomain($cookieString);

                $this->cookieJar->setCookie(SetCookie::fromString($cookieString));
            }
        }

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
    public function setCookieJar(CookieJarInterface $cookieJar)
    {
        $this->cookieJar = $cookieJar;

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
     * Request dispatch helper.
     *
     * @param  string $method
     * @param  string $uri
     * @param  array|null $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function dispatchRequest($method, $uri, array $options = null)
    {
        $options or $options = $this->buildOptions();

        return $this->client->$method($uri, $options);
    }

    /**
     * Builds and returns an array of request options.
     *
     * @return array
     */
    protected function buildOptions()
    {
        $dataKey = $this->method == 'get' ? 'query' : 'form_params';

        return array_replace($this->requestOptions, [
            $dataKey      => $this->data,
            'delay'       => $this->delay,
            'headers'     => $this->headers,
            'cookies'     => $this->cookieJar,
            'http_errors' => $this->httpErrors,
        ]);
    }

    /**
     * Alters the response instance before it leaves the context.
     *
     * Derived classes may override this method to manipulate the response object;
     * whatever they do, they must return an instance of ResponseInterface.
     *
     * @param  ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function alterResponse(ResponseInterface $response)
    {
        return $response;
    }

    /**
     * Ensures that the cookie string contains a Domain part.
     *
     * @param  string $cookieString
     *
     * @return string
     */
    private function ensureCookieDomain($cookieString)
    {
        if (strpos($cookieString, 'Domain') === false) {
            $cookieString .= "; domain={$this->cookieDomain}";
        }

        return trim($cookieString);
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
