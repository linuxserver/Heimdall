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

use InvalidArgumentException;

/**
 * This is the authenticator factory class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class AuthenticatorFactory
{
    /**
     * Make a new authenticator instance.
     *
     * @param string $method
     *
     * @throws \InvalidArgumentException
     *
     * @return \GrahamCampbell\GitHub\Authenticators\AuthenticatorInterface
     */
    public function make(string $method)
    {
        switch ($method) {
            case 'application':
                return new ApplicationAuthenticator(); // AUTH_URL_CLIENT_ID
            case 'jwt':
                return new JwtAuthenticator(); // AUTH_JWT
            case 'password':
                return new PasswordAuthenticator(); // AUTH_HTTP_PASSWORD
            case 'token':
                return new TokenAuthenticator(); // AUTH_HTTP_TOKEN
        }

        throw new InvalidArgumentException("Unsupported authentication method [$method].");
    }
}
