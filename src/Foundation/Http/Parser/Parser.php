<?php

namespace Profounder\Foundation\Http\Parser;

use Psr\Http\Message\ResponseInterface;
use Profounder\Exception\InvalidSession;
use Profounder\Exception\InvalidResponse;
use Profounder\Exception\InvalidArgument;

class Parser implements ParserContract
{
    /**
     * Response instance.
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @inheritdoc
     */
    public function parse(ResponseInterface $response = null, $validate = true)
    {
        $response && $this->setResponse($response);

        if (! $this->response) {
            throw new InvalidArgument('No response is set for the parser.');
        }

        $validate and $this->validate();

        return $this->parseBody($this->prepareBodyForParse());
    }

    /**
     * @inheritdoc
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Actual response body parser.
     *
     * Derived classes may override this method to implement their own parsing logic.
     *
     * @param  mixed $body
     *
     * @return mixed
     */
    protected function parseBody($body)
    {
        return $body;
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
        if ($this->responseContains('encountered a critical error') || $this->responseContains('runtime error')) {
            throw InvalidResponse::critical();
        }

        if ($this->responseContains('start a free trial') || $this->responseContains('not currently logged in')) {
            throw InvalidSession::expired();
        }

        if ($this->responseContains('one or more of your selected products were not found')) {
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

    /**
     * Checks if the response body contains a specific substring.
     *
     * @param  string $needle
     *
     * @return bool
     */
    protected function responseContains($needle)
    {
        return stripos($this->responseBody(), $needle) !== false;
    }

    /**
     * Matches the response body text against the passed pattern.
     *
     * @param  string $regex
     *
     * @return bool
     */
    protected function responseMatches($regex)
    {
        return (bool) preg_match($regex, $this->responseBody());
    }
}
