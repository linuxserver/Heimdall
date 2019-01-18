<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Client\Credentials;

/**
 * Class Basic
 * Read-only container for api key and secret.
 */
class Basic extends AbstractCredentials implements CredentialsInterface
{
    /**
     * Create a credential set with an API key and secret.
     *
     * @param string $key
     * @param string $secret
     */
    public function __construct($key, $secret)
    {
        $this->credentials['api_key'] = $key;
        $this->credentials['api_secret'] = $secret;
    }
}