<?php

namespace Profounder\Service\Identity;

use Profounder\Exception\InvalidSession;

class Pool implements PoolContract
{
    /**
     * Sessions array of username, password and cookie.
     *
     * @var array
     */
    protected $pool;

    /**
     * Pool constructor.
     *
     * @param array|null $pool
     */
    public function __construct(array $pool = null)
    {
        $this->setPool($pool);
    }

    /**
     * @inheritdoc
     */
    public function setPool(array $pool)
    {
        $this->pool = $pool;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function retrieve($id)
    {
        if (isset($this->pool[$id])) {
            return Identity::create($this->pool[$id]);
        }

        throw new InvalidSession('Out of identity sessions. Add more sessions or reduce workers.');
    }

    /**
     * @inheritdoc
     */
    public function random()
    {
        return $this->retrieve(array_rand($this->pool));
    }
}
