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
 * This is the abstract authenticator class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
abstract class AbstractAuthenticator
{
    /**
     * The client to perform the authentication on.
     *
     * @var \Github\Client|null
     */
    protected $client;

    /**
     * Set the client to perform the authentication on.
     *
     * @param \Github\Client $client
     *
     * @return \GrahamCampbell\GitHub\Authenticators\AuthenticatorInterface
     */
    public function with(Client $client)
    {
        $this->client = $client;

        return $this;
    }
}
