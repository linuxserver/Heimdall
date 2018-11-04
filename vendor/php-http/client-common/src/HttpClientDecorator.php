<?php

namespace Http\Client\Common;

use Http\Client\HttpClient;
use Psr\Http\Message\RequestInterface;

/**
 * Decorates an HTTP Client.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
trait HttpClientDecorator
{
    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * {@inheritdoc}
     *
     * @see HttpClient::sendRequest
     */
    public function sendRequest(RequestInterface $request)
    {
        return $this->httpClient->sendRequest($request);
    }
}
