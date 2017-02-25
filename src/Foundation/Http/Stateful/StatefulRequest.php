<?php

namespace Profounder\Foundation\Http\Stateful;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJarInterface;
use Psr\Http\Message\ResponseInterface;
use Profounder\Foundation\Http\Request;

abstract class StatefulRequest extends Request implements StatefulRequestContract
{
    /**
     * State instance.
     *
     * @var State
     */
    protected $state;

    /**
     * State response instance.
     *
     * @var ResponseInterface
     */
    protected $stateResponse;

    /**
     * StateParser instance.
     *
     * @var StateParser
     */
    protected $stateParser;

    /**
     * URI to request to obtain the state values.
     *
     * @var string
     */
    protected $stateUri;

    /**
     * Request method to obtain the state values.
     *
     * @var string
     */
    protected $stateMethod;

    /**
     * State request options array.
     *
     * @var array
     */
    protected $stateRequestOptions = ['http_errors' => true];

    /**
     * StatefulRequest constructor.
     *
     * @param  ClientInterface $client
     * @param  CookieJarInterface $cookieJar
     * @param  StateParser $parser
     */
    public function __construct(ClientInterface $client, CookieJarInterface $cookieJar, StateParser $parser)
    {
        $this->stateParser = $parser;
        $this->initializeStateProperties();

        parent::__construct($client, $cookieJar);
    }

    /**
     * @inheritdoc
     */
    public function dispatch($delay = null, $cookie = null)
    {
        $this->state = $this->parseStateResponse($this->dispatchStateRequest());

        $this->validateState($this->state, $this->stateResponse);

        $this->withData($this->state->data);
        $this->withCookie($this->state->cookie);

        return parent::dispatch($delay, $cookie);
    }

    /**
     * @inheritdoc
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Validates the extracted state instance and the response.
     *
     * @param  State $state
     * @param  ResponseInterface $response
     *
     * @return void
     */
    protected function validateState(State $state, ResponseInterface $response)
    {
        //
    }

    /**
     * Dispatches the state request.
     *
     * @return ResponseInterface
     */
    protected function dispatchStateRequest()
    {
        return $this->stateResponse = $this->dispatchRequest(
            $this->stateMethod,
            $this->stateUri,
            $this->stateRequestOptions
        );
    }

    /**
     * Parses the state response and returns an State instance.
     *
     * @param  ResponseInterface $response
     *
     * @return State
     */
    protected function parseStateResponse(ResponseInterface $response)
    {
        return $this->stateParser->parse($response, false);
    }

    /**
     * Initializes state properties only if they're not set.
     *
     * @return void
     */
    private function initializeStateProperties()
    {
        isset($this->stateUri)    or $this->stateUri    = $this->uri;
        isset($this->stateMethod) or $this->stateMethod = $this->method;
    }
}
