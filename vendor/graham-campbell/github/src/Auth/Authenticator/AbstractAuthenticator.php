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
 * This is the abstract authenticator class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
abstract class AbstractAuthenticator implements AuthenticatorInterface
{
    /**
     * The client to perform the authentication on.
     *
     * @var \Github\Client|null
     */
    private ?Client $client = null;

    /**
     * Set the client to perform the authentication on.
     *
     * @param \Github\Client $client
     *
     * @return \GrahamCampbell\GitHub\Auth\Authenticator\AuthenticatorInterface
     */
    public function with(Client $client): AuthenticatorInterface
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return \Github\Client
     */
    protected function getClient(): Client
    {
        if (!$this->client) {
            throw new InvalidArgumentException('The client instance was not given to the authenticator.');
        }

        return $this->client;
    }
}
