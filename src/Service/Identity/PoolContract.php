<?php
namespace Profounder\Service\Identity;

use Profounder\Exception\InvalidSession;

interface PoolContract
{
    /**
     * Sets pool.
     *
     * @param array $pool
     *
     * @return PoolContract
     */
    public function setPool(array $pool);

    /**
     * Returns an Identity instance by ID.
     *
     * @param int $id
     *
     * @throws InvalidSession
     *
     * @return Identity
     */
    public function retrieve($id = 0);

    /**
     * Retrieves a random Identity instance out of the pool.
     *
     * @return Identity
     */
    public function random();
}
