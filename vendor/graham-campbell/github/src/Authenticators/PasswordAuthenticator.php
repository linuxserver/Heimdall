<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitHub.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\GitHub\Authenticators;

use GitHub\Client;
use InvalidArgumentException;

/**
 * This is the password authenticator class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class PasswordAuthenticator extends AbstractAuthenticator implements AuthenticatorInterface
{
    /**
     * Authenticate the client, and return it.
     *
     * @param string[] $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Github\Client
     */
    public function authenticate(array $config)
    {
        if (!$this->client) {
            throw new InvalidArgumentException('The client instance was not given to the password authenticator.');
        }

        if (!array_key_exists('username', $config) || !array_key_exists('password', $config)) {
            throw new InvalidArgumentException('The password authenticator requires a username and password.');
        }

        $this->client->authenticate($config['username'], $config['password'], Client::AUTH_HTTP_PASSWORD);

        return $this->client;
    }
}
