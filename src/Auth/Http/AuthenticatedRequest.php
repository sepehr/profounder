<?php

namespace Profounder\Auth\Http;

use Profounder\Request;
use Profounder\RequestContract;
use GuzzleHttp\ClientInterface;
use Profounder\Exception\InvalidSession;
use GuzzleHttp\Cookie\CookieJarInterface;
use Profounder\Auth\Session\StoreContract;

abstract class AuthenticatedRequest extends Request implements RequestContract
{
    /**
     * Store instance.
     *
     * @var StoreContract
     */
    protected $store;

    /**
     * Session instance.
     *
     * @var \Profounder\Auth\Session\Session|null
     */
    protected $session;

    /**
     * @inheritdoc
     *
     * @param  StoreContract $store
     */
    public function __construct(StoreContract $store, ClientInterface $client, CookieJarInterface $cookieJar)
    {
        $this->store   = $store;
        $this->session = $this->store->retrieve();

        parent::__construct($client, $cookieJar);
    }

    /**
     * @inheritdoc
     *
     * @throws InvalidSession
     */
    public function dispatch($delay = null, $cookie = null)
    {
        if ($this->session) {
            $this->withCookie($this->session->cookie);

            return parent::dispatch($delay, $cookie);
        }

        throw InvalidSession::notFound();
    }
}
