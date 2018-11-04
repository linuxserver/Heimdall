<?php

namespace Github\HttpClient\Plugin;

use Github\Client;
use Github\Exception\RuntimeException;
use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;

/**
 * Add authentication to the request.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Authentication implements Plugin
{
    private $tokenOrLogin;
    private $password;
    private $method;

    public function __construct($tokenOrLogin, $password, $method)
    {
        $this->tokenOrLogin = $tokenOrLogin;
        $this->password = $password;
        $this->method = $method;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        switch ($this->method) {
            case Client::AUTH_HTTP_PASSWORD:
                $request = $request->withHeader(
                    'Authorization',
                    sprintf('Basic %s', base64_encode($this->tokenOrLogin.':'.$this->password))
                );
                break;

            case Client::AUTH_HTTP_TOKEN:
                $request = $request->withHeader('Authorization', sprintf('token %s', $this->tokenOrLogin));
                break;

            case Client::AUTH_URL_CLIENT_ID:
                $uri = $request->getUri();
                $query = $uri->getQuery();

                $parameters = [
                    'client_id' => $this->tokenOrLogin,
                    'client_secret' => $this->password,
                ];

                $query .= empty($query) ? '' : '&';
                $query .= utf8_encode(http_build_query($parameters, '', '&'));

                $uri = $uri->withQuery($query);
                $request = $request->withUri($uri);
                break;

            case Client::AUTH_URL_TOKEN:
                $uri = $request->getUri();
                $query = $uri->getQuery();

                $parameters = ['access_token' => $this->tokenOrLogin];

                $query .= empty($query) ? '' : '&';
                $query .= utf8_encode(http_build_query($parameters, '', '&'));

                $uri = $uri->withQuery($query);
                $request = $request->withUri($uri);
                break;

            case Client::AUTH_JWT:
                $request = $request->withHeader('Authorization', sprintf('Bearer %s', $this->tokenOrLogin));
                break;

            default:
                throw new RuntimeException(sprintf('%s not yet implemented', $this->method));
                break;
        }

        return $next($request);
    }
}
