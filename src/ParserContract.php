<?php

namespace Profounder;

use Psr\Http\Message\ResponseInterface;

interface ParserContract
{
    /**
     * Validates and parses a response instance.
     *
     * @param  ResponseInterface|null $response
     *
     * @return mixed
     *
     * @throws \Profounder\Exception\ExceptionContract
     */
    public function parse(ResponseInterface $response = null);

    /**
     * Sets response instance.
     *
     * @param  ResponseInterface $response
     *
     * @return ParserContract
     */
    public function setResponse(ResponseInterface $response);
}
