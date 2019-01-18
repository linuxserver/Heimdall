<?php

namespace Http\Client\Common;

use Http\Client\Exception;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Convenience HTTP client that integrates the MessageFactory in order to send
 * requests in the following form:.
 *
 * $client
 *     ->get('/foo')
 *     ->post('/bar')
 * ;
 *
 * The client also exposes the sendRequest methods of the wrapped HttpClient.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 * @author David Buchmann <mail@davidbu.ch>
 */
class HttpMethodsClient implements HttpClient
{
    /**
     * @var HttpClient|ClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @param HttpClient|ClientInterface $httpClient     The client to send requests with
     * @param RequestFactory             $requestFactory The message factory to create requests
     */
    public function __construct($httpClient, RequestFactory $requestFactory)
    {
        if (!($httpClient instanceof HttpClient) && !($httpClient instanceof ClientInterface)) {
            throw new \LogicException('Client must be an instance of Http\\Client\\HttpClient or Psr\\Http\\Client\\ClientInterface');
        }

        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
    }

    /**
     * Sends a GET request.
     *
     * @param string|UriInterface $uri
     * @param array               $headers
     *
     * @throws Exception
     *
     * @return ResponseInterface
     */
    public function get($uri, array $headers = [])
    {
        return $this->send('GET', $uri, $headers, null);
    }

    /**
     * Sends an HEAD request.
     *
     * @param string|UriInterface $uri
     * @param array               $headers
     *
     * @throws Exception
     *
     * @return ResponseInterface
     */
    public function head($uri, array $headers = [])
    {
        return $this->send('HEAD', $uri, $headers, null);
    }

    /**
     * Sends a TRACE request.
     *
     * @param string|UriInterface $uri
     * @param array               $headers
     *
     * @throws Exception
     *
     * @return ResponseInterface
     */
    public function trace($uri, array $headers = [])
    {
        return $this->send('TRACE', $uri, $headers, null);
    }

    /**
     * Sends a POST request.
     *
     * @param string|UriInterface         $uri
     * @param array                       $headers
     * @param string|StreamInterface|null $body
     *
     * @throws Exception
     *
     * @return ResponseInterface
     */
    public function post($uri, array $headers = [], $body = null)
    {
        return $this->send('POST', $uri, $headers, $body);
    }

    /**
     * Sends a PUT request.
     *
     * @param string|UriInterface         $uri
     * @param array                       $headers
     * @param string|StreamInterface|null $body
     *
     * @throws Exception
     *
     * @return ResponseInterface
     */
    public function put($uri, array $headers = [], $body = null)
    {
        return $this->send('PUT', $uri, $headers, $body);
    }

    /**
     * Sends a PATCH request.
     *
     * @param string|UriInterface         $uri
     * @param array                       $headers
     * @param string|StreamInterface|null $body
     *
     * @throws Exception
     *
     * @return ResponseInterface
     */
    public function patch($uri, array $headers = [], $body = null)
    {
        return $this->send('PATCH', $uri, $headers, $body);
    }

    /**
     * Sends a DELETE request.
     *
     * @param string|UriInterface         $uri
     * @param array                       $headers
     * @param string|StreamInterface|null $body
     *
     * @throws Exception
     *
     * @return ResponseInterface
     */
    public function delete($uri, array $headers = [], $body = null)
    {
        return $this->send('DELETE', $uri, $headers, $body);
    }

    /**
     * Sends an OPTIONS request.
     *
     * @param string|UriInterface         $uri
     * @param array                       $headers
     * @param string|StreamInterface|null $body
     *
     * @throws Exception
     *
     * @return ResponseInterface
     */
    public function options($uri, array $headers = [], $body = null)
    {
        return $this->send('OPTIONS', $uri, $headers, $body);
    }

    /**
     * Sends a request with any HTTP method.
     *
     * @param string                      $method  HTTP method to use
     * @param string|UriInterface         $uri
     * @param array                       $headers
     * @param string|StreamInterface|null $body
     *
     * @throws Exception
     *
     * @return ResponseInterface
     */
    public function send($method, $uri, array $headers = [], $body = null)
    {
        return $this->sendRequest($this->requestFactory->createRequest(
            $method,
            $uri,
            $headers,
            $body
        ));
    }

    /**
     * Forward to the underlying HttpClient.
     *
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request)
    {
        return $this->httpClient->sendRequest($request);
    }
}
