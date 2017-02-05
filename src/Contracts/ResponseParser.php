<?php

namespace Profounder\Contracts;

use Psr\Http\Message\ResponseInterface;

interface ResponseParser
{
    /**
     * Validates and parses the response instance.
     *
     * @param  ResponseInterface|null $response
     *
     * @return mixed
     */
    public function parse(ResponseInterface $response = null);

    /**
     * Sets response instance.
     *
     * @param  ResponseInterface $response
     *
     * @return ResponseParser
     */
    public function setResponse(ResponseInterface $response);
}
