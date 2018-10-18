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

use Github\Client;

/**
 * This is the authenticator interface.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
interface AuthenticatorInterface
{
    /**
     * Set the client to perform the authentication on.
     *
     * @param \Github\Client $client
     *
     * @return \GrahamCampbell\GitHub\Authenticators\AuthenticatorInterface
     */
    public function with(Client $client);

    /**
     * Authenticate the client, and return it.
     *
     * @param string[] $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \Github\Client
     */
    public function authenticate(array $config);
}
