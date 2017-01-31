<?php

namespace Profounder\Query;

use Psr\Http\Message\ResponseInterface;
use Profounder\Exception\InvalidSession;
use Profounder\Exception\InvalidResponse;
use Profounder\Exception\InvalidArgument;

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
     * Static factory method.
     *
     * @return ResponseParser
     */
    public static function create()
    {
        return new static;
    }

    /**
     * Parses response into a JSON object.
     *
     * @param  ResponseInterface|null $response
     *
     * @return array
     *
     * @throws InvalidArgument
     */
    public function parse(ResponseInterface $response = null)
    {
        $response && $this->setResponse($response);

        if (! $this->response) {
            throw new InvalidArgument('No response is set for the parser.');
        }

        $parsed = $this->validate($this->parseJson());

        $parsed = $parsed[$this->resultsKey] ?: [];

        return $parsed;
    }

    /**
     * Response setter.
     *
     * @param  ResponseInterface $response
     *
     * @return ResponseParser
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
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
            throw InvalidResponse::critical();
        }

        $parsedJson = json_decode($content, true);

        if (is_null($parsedJson)) {
            throw InvalidResponse::invalidJson();
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
            throw InvalidSession::expired();
        }

        if (! empty($json['ErrorMessage'])) {
            throw InvalidResponse::remoteError($json['ErrorMessage']);
        }

        return $json;
    }
}
