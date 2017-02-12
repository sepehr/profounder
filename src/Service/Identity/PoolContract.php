<?php
namespace Profounder\Service\Identity;

use Profounder\Exception\InvalidSession;

interface PoolContract
{
    /**
     * Sets pool.
     *
     * @param  array $pool
     *
     * @return PoolContract
     */
    public function setPool(array $pool);

    /**
     * Returns an Identity instance by ID.
     *
     * @param  int $id
     *
     * @return Identity
     *
     * @throws InvalidSession
     */
    public function retrieve($id);

    /**
     * Retrieves a random Identity instance out of the pool.
     *
     * @return Identity
     */
    public function random();
}
