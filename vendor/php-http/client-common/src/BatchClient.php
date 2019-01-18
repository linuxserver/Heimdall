<?php

namespace Http\Client\Common;

use Http\Client\Exception;
use Http\Client\HttpClient;
use Http\Client\Common\Exception\BatchException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * BatchClient allow to sends multiple request and retrieve a Batch Result.
 *
 * This implementation simply loops over the requests and uses sendRequest with each of them.
 *
 * @author Joel Wurtz <jwurtz@jolicode.com>
 */
class BatchClient implements HttpClient
{
    /**
     * @var HttpClient|ClientInterface
     */
    private $client;

    /**
     * @param HttpClient|ClientInterface  $client
     */
    public function __construct($client)
    {
        if (!($client instanceof HttpClient) && !($client instanceof ClientInterface)) {
            throw new \LogicException('Client must be an instance of Http\\Client\\HttpClient or Psr\\Http\\Client\\ClientInterface');
        }

        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request)
    {
        return $this->client->sendRequest($request);
    }

    /**
     * Send several requests.
     *
     * You may not assume that the requests are executed in a particular order. If the order matters
     * for your application, use sendRequest sequentially.
     *
     * @param RequestInterface[] The requests to send
     *
     * @return BatchResult Containing one result per request
     *
     * @throws BatchException If one or more requests fails. The exception gives access to the
     *                        BatchResult with a map of request to result for success, request to
     *                        exception for failures
     */
    public function sendRequests(array $requests)
    {
        $batchResult = new BatchResult();

        foreach ($requests as $request) {
            try {
                $response = $this->sendRequest($request);
                $batchResult = $batchResult->addResponse($request, $response);
            } catch (Exception $e) {
                $batchResult = $batchResult->addException($request, $e);
            }
        }

        if ($batchResult->hasExceptions()) {
            throw new BatchException($batchResult);
        }

        return $batchResult;
    }
}
