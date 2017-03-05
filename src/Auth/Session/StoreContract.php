<?php

namespace Profounder\Auth\Session;

interface StoreContract
{
    /**
     * Stores Session instance into the store.
     *
     * @param SessionContract $session
     *
     * @return bool
     */
    public function save(SessionContract $session);

    /**
     * Retrieves Session instance from store.
     *
     * @return SessionContract|null
     */
    public function retrieve();
}
