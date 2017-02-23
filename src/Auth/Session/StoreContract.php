<?php

namespace Profounder\Auth\Session;

interface StoreContract
{
    /**
     * Stores Session instance into the store.
     *
     * @param  Session $session
     *
     * @return bool
     */
    public function save(Session $session);

    /**
     * Retrieves Session instance from store.
     *
     * @return Session|null
     */
    public function retrieve();
}
