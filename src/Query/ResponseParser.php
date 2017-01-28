<?php

namespace Profounder\Query;

use Psr\Http\Message\ResponseInterface;
use Profounder\Exceptions\InvalidSession;
use Profounder\Exceptions\InvalidResponse;

class ResponseParser
{
    /**
     * Response object.
     *
     * @var ResponseInterface
     */
    private $response;

    /**
     * Results key in remote response.
     *
     * @var string
     */
    private $resultsKey = 'Results';

    /**
     * ResponseParser constructor.
     *
     * @param  ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Static factory method.
     *
     * @param  ResponseInterface $response
     *
     * @return ResponseParser
     */
    public static function create(ResponseInterface $response)
    {
        return new static($response);
    }

    /**
     * Parses response into a JSON object.
     *
     * @return array
     */
    public function parse()
    {
        $parsed = $this->validate($this->parseJson());

        $parsed = $parsed[$this->resultsKey] ?: [];

        return $parsed;
    }

    /**
     * Parses a Guzzle response into an array.
     *
     * @return array
     *
     * @throws InvalidResponse
     */
    private function parseJson()
    {
        $content = (string) $this->response->getBody();

        if (strpos($content, 'web server encountered a critical error')) {
            throw new InvalidResponse('Remote webserver critical hiccup! renew the session to work around this.');
        }

        $parsedJson = json_decode($content, true);
        if (is_null($parsedJson)) {
            throw new InvalidResponse('Retrieved invalid JSON response.');
        }

        return $parsedJson;
    }

    /**
     * Validates the JSON-decoded response array.
     *
     * @param  array $json
     *
     * @return array
     *
     * @throws InvalidResponse
     * @throws InvalidSession
     */
    private function validate(array $json)
    {
        if ($json['UserIsLoggedOut']) {
            throw new InvalidSession(
                'Either the session has expired or its trial period is over. Feed me some fresh sessions.'
            );
        }

        if (! empty($json['ErrorMessage'])) {
            throw new InvalidResponse("Remote error: {$json['ErrorMessage']}");
        }

        return $json;
    }
}
