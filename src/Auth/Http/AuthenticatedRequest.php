<?php

namespace Profounder\Auth\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJarInterface;
use Profounder\Exception\InvalidSession;
use Profounder\Auth\Session\StoreContract;
use Profounder\Foundation\Http\Request;
use Profounder\Foundation\Http\RequestContract;

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
     * @var \Profounder\Auth\Session\SessionContract|null
     */
    protected $session;

    /**
     * @inheritdoc
     *
     * @param StoreContract $store
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
            $this->withCookie($this->session->getCookie());

            return parent::dispatch($delay, $cookie);
        }

        throw InvalidSession::notFound();
    }
}
