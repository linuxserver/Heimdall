<?php

namespace Http\Adapter\Guzzle6;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Psr\Http\Message\RequestInterface;

/**
 * HTTP Adapter for Guzzle 6.
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class Client implements HttpClient, HttpAsyncClient
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface|null $client
     */
    public function __construct(ClientInterface $client = null)
    {
        if (!$client) {
            $client = static::buildClient();
        }

        $this->client = $client;
    }

    /**
     * Factory method to create the guzzle 6 adapter with custom configuration for guzzle.
     *
     * @param array $config Configuration to create guzzle with.
     *
     * @return Client
     */
    public static function createWithConfig(array $config)
    {
        return new self(static::buildClient($config));
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request)
    {
        $promise = $this->sendAsyncRequest($request);

        return $promise->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function sendAsyncRequest(RequestInterface $request)
    {
        $promise = $this->client->sendAsync($request);

        return new Promise($promise, $request);
    }

    /**
     * Build the guzzle client instance.
     *
     * @param array $config Additional configuration
     *
     * @return GuzzleClient
     */
    private static function buildClient(array $config = [])
    {
        $handlerStack = new HandlerStack(\GuzzleHttp\choose_handler());
        $handlerStack->push(Middleware::prepareBody(), 'prepare_body');
        $config = array_merge(['handler' => $handlerStack], $config);

        return new GuzzleClient($config);
    }
}
