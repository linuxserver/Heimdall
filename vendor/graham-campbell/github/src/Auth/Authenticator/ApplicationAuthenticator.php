<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitHub.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\GitHub\Auth\Authenticator;

use Github\Client;
use InvalidArgumentException;

/**
 * This is the application authenticator class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class ApplicationAuthenticator extends AbstractAuthenticator
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
            throw new InvalidArgumentException('The client instance was not given to the application authenticator.');
        }

        if (!array_key_exists('clientId', $config) || !array_key_exists('clientSecret', $config)) {
            throw new InvalidArgumentException('The application authenticator requires a client id and secret.');
        }

        $this->client->authenticate($config['clientId'], $config['clientSecret'], Client::AUTH_CLIENT_ID);

        return $this->client;
    }
}
