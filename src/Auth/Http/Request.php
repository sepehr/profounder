<?php

namespace Profounder\Auth\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJarInterface;
use Psr\Http\Message\ResponseInterface;
use Profounder\Exception\InvalidResponse;
use Profounder\Service\Identity\IdentityContract;
use Profounder\Foundation\Http\Stateful\StateContract;
use Profounder\Foundation\Http\Stateful\StatefulRequest;
use Profounder\Foundation\Http\Stateful\StateParserContract;

class Request extends StatefulRequest implements RequestContract
{
    /**
     * @inheritdoc
     */
    protected $stateMethod = 'get';

    /**
     * @inheritdoc
     */
    protected $method = 'post';

    /**
     * @inheritdoc
     */
    protected $uri = 'http://www.profound.com/Home.aspx';

    /**
     * @inheritdoc
     */
    protected $headers = [
        'Referer'      => 'http://www.profound.com/Home.aspx',
        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
    ];

    /**
     * @inheritdoc
     */
    protected $data = [
        'LoginBox$txtUserId'     => null,
        'LoginBox$txtPassword'   => null,
        'LoginBox$cbxRememberMe' => 'on',
        'LoginBox$btnLogin'      => 'Login',
    ];

    /**
     * @inheritdoc
     *
     * If we follow the redirect, we'll be loosing response cookies.
     */
    protected $requestOptions = ['allow_redirects' => false];

    /**
     * @inheritdoc
     *
     * @param  IdentityContract $identity
     */
    public function __construct(
        ClientInterface $client,
        CookieJarInterface $cookieJar,
        StateParserContract $parser,
        IdentityContract $identity
    ) {
        $this->actAs($identity);

        parent::__construct($client, $cookieJar, $parser);
    }

    /**
     * @inheritdoc
     */
    public function actAs(IdentityContract $identity)
    {
        $this->data['LoginBox$txtUserId']   = $identity->getUsername();
        $this->data['LoginBox$txtPassword'] = $identity->getPassword();

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws InvalidResponse
     */
    protected function validateState(StateContract $state, ResponseInterface $response)
    {
        if (! $this->validateStateData($state) || ! $this->validateStateCookies($state)) {
            throw new InvalidResponse('Could not fetch the required state data and/or cookies.');
        }
    }

    /**
     * Validates state data.
     *
     * @param  StateContract $state
     *
     * @return bool
     */
    private function validateStateData(StateContract $state)
    {
        return ! empty($state->getData('__VIEWSTATE'));
    }

    /**
     * Validates state cookies.
     *
     * @param  StateContract $state
     *
     * @return bool
     */
    private function validateStateCookies(StateContract $state)
    {
        return ! empty($state->getCookie());
    }
}
