<?php

namespace Profounder\Foundation\Http\Parser;

use Psr\Http\Message\ResponseInterface;

interface ParserContract
{
    /**
     * Validates and parses a response instance.
     *
     * @param  ResponseInterface|null $response
     * @param  bool $validate
     *
     * @return mixed
     *
     * @throws \Profounder\Exception\ExceptionContract
     */
    public function parse(ResponseInterface $response = null, $validate = true);

    /**
     * Sets response instance.
     *
     * @param  ResponseInterface $response
     *
     * @return ParserContract
     */
    public function setResponse(ResponseInterface $response);
}
