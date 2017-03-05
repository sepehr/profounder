<?php

namespace Profounder\Service\Identity;

use Profounder\Foundation\Support\Fluent;

/**
 * Class representing a user identity.
 *
 * @property  array $cookie
 * @property  string $username
 * @property  string $password
 */
class Identity extends Fluent implements IdentityContract
{
    /**
     * @inheritdoc
     */
    public static function createWithCredentials($username, $password)
    {
        return new static([
            'username' => $username,
            'password' => $password,
            'cookie'   => [],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritdoc
     */
    public function getCookie()
    {
        return $this->cookie;
    }
}
