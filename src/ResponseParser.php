<?php

namespace Profounder;

use Psr\Http\Message\ResponseInterface;
use Profounder\Exception\InvalidSession;
use Profounder\Exception\InvalidResponse;
use Profounder\Exception\InvalidArgument;
use Profounder\Contracts\ResponseParser as ResponseParserContract;

abstract class ResponseParser implements ResponseParserContract
{
    /**
     * Response instance.
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Actual response body parser.
     *
     * Derived classes should override this method to implement their own parsing logic.
     *
     * @param  mixed $body
     *
     * @return mixed
     */
    abstract protected function parseBody($body);

    /**
     * Validates and parses a response instance.
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

        $this->validate();

        return $this->parseBody($this->prepareBodyForParse());
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
     * Runs basic checks against response instance.
     *
     * @return void
     *
     * @throws InvalidResponse
     * @throws InvalidSession
     */
    protected function validate()
    {
        $content = $this->responseBody();

        if (strpos($content, 'web server encountered a critical error') || strpos($content, 'Runtime Error')) {
            throw InvalidResponse::critical();
        }

        if (strpos($content, 'Sign In')) {
            throw InvalidSession::expired();
        }

        if (strpos($content, 'One or more of your selected products were not found')) {
            throw InvalidResponse::notFound();
        }
    }

    /**
     * Prepares response body for being parsed.
     *
     * @return mixed
     */
    protected function prepareBodyForParse()
    {
        return $this->responseBody();
    }

    /**
     * Returns response body text.
     *
     * @return string
     */
    protected function responseBody()
    {
        return (string) $this->response->getBody();
    }
}