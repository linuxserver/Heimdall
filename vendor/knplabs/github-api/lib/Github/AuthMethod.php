<?php

namespace Github;

final class AuthMethod
{
    /**
     * Authenticate using a client_id/client_secret combination.
     *
     * @var string
     */
    public const CLIENT_ID = 'client_id_header';

    /**
     * Authenticate using a GitHub access token.
     *
     * @var string
     */
    public const ACCESS_TOKEN = 'access_token_header';

    /**
     * Constant for authentication method.
     *
     * Indicates JSON Web Token authentication required for GitHub apps access
     * to the API.
     *
     * @var string
     */
    public const JWT = 'jwt';
}
