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
 * This is the token authenticator class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class TokenAuthenticator extends AbstractAuthenticator implements AuthenticatorInterface
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
            throw new InvalidArgumentException('The client instance was not given to the token authenticator.');
        }

        if (!array_key_exists('token', $config)) {
            throw new InvalidArgumentException('The token authenticator requires a token.');
        }

        $this->client->authenticate($config['token'], Client::AUTH_HTTP_TOKEN);

        return $this->client;
    }
}
