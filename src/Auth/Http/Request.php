<?php

namespace Profounder\Auth\Http;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJarInterface;
use Profounder\Service\Identity\Identity;
use Profounder\Exception\InvalidResponse;
use Profounder\Foundation\Http\Stateful\State;
use Profounder\Foundation\Http\Stateful\StateParser;
use Profounder\Foundation\Http\Stateful\StatefulRequest;

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
     * @param  Identity $identity
     */
    public function __construct(
        ClientInterface $client,
        CookieJarInterface $cookieJar,
        StateParser $parser,
        Identity $identity
    ) {
        $this->actAs($identity);

        parent::__construct($client, $cookieJar, $parser);
    }

    /**
     * @inheritdoc
     */
    public function actAs(Identity $identity)
    {
        $this->data['LoginBox$txtUserId']   = $identity->username;
        $this->data['LoginBox$txtPassword'] = $identity->password;

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws InvalidResponse
     */
    protected function validateState(State $state, ResponseInterface $response)
    {
        if (! $this->validateStateData($state) || ! $this->validateStateCookies($state)) {
            throw new InvalidResponse('Could not fetch the required state data and/or cookies.');
        }
    }

    /**
     * Validates state data.
     *
     * @param  State $state
     *
     * @return bool
     */
    private function validateStateData(State $state)
    {
        return ! empty($state->data['__VIEWSTATE']);
    }

    /**
     * Validates state cookies.
     *
     * @param  State $state
     *
     * @return bool
     */
    private function validateStateCookies(State $state)
    {
        return ! empty($state->cookie);
    }
}
