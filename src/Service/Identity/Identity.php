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
class Identity extends Fluent
{
    /**
     * Static factory method to create an instance with username and password.
     *
     * @param  string  $username
     * @param  string  $password
     *
     * @return $this
     */
    public static function createWithCredentials($username, $password)
    {
        return new static([
            'username' => $username,
            'password' => $password,
            'cookie'   => [],
        ]);
    }
}
