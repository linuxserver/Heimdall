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
    protected $client;

    /**
     * Set the client to perform the authentication on.
     *
     * @param \Github\Client $client
     *
     * @return \GrahamCampbell\GitHub\Auth\Authenticator\AuthenticatorInterface
     */
    public function with(Client $client)
    {
        $this->client = $client;

        return $this;
    }
}
