<?php

namespace Profounder\Auth\Session;

use Profounder\Core\StorageContract;

class Store implements StoreContract
{
    /**
     * Storage instance.
     *
     * @var StorageContract
     */
    protected $storage;

    /**
     * Session filename.
     *
     * @var string
     */
    protected $sessionFile = 'session.txt';

    /**
     * Store constructor.
     *
     * @param  StorageContract $storage
     */
    public function __construct(StorageContract $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @inheritdoc
     */
    public function save(Session $session)
    {
        return $this->storage->put(
            $this->sessionFile,
            $this->serialize($session)
        );
    }

    /**
     * @inheritdoc
     */
    public function retrieve()
    {
        if ($session = $this->storage->get($this->sessionFile)) {
            return $this->unserialize($session);
        }

        return null;
    }

    /**
     * Serializes a Session instance.
     *
     * @param  Session $session
     *
     * @return string
     */
    private function serialize($session)
    {
        return serialize($session);
    }

    /**
     * Unserializes a Session instance.
     *
     * @param  string $serializedSession
     *
     * @return Session
     */
    private function unserialize($serializedSession)
    {
        return unserialize($serializedSession);
    }
}
