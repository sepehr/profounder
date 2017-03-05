<?php

namespace Profounder\Service\Identity;

use Profounder\Foundation\Support\FluentContract;

interface IdentityContract extends FluentContract
{
    /**
     * Static factory method to create an instance with username and password.
     *
     * @param string $username
     * @param string $password
     *
     * @return static
     */
    public static function createWithCredentials($username, $password);

    /**
     * Getter for username property.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Getter for password property.
     *
     * @return string
     */
    public function getPassword();

    /**
     * Getter for cookie property.
     *
     * @return array
     */
    public function getCookie();
}
