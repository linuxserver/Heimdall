<?php

namespace Http\Client\Common;

use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Psr\Http\Message\RequestInterface;
use Http\Client\Exception;

/**
 * A HttpClientPoolItem represent a HttpClient inside a Pool.
 *
 * It is disabled when a request failed and can be reenable after a certain number of seconds
 * It also keep tracks of the current number of request the client is currently sending (only usable for async method)
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class HttpClientPoolItem implements HttpClient, HttpAsyncClient
{
    /**
     * @var int Number of request this client is currently sending
     */
    private $sendingRequestCount = 0;

    /**
     * @var \DateTime|null Time when this client has been disabled or null if enable
     */
    private $disabledAt;

    /**
     * @var int|null Number of seconds after this client is reenable, by default null: never reenable this client
     */
    private $reenableAfter;

    /**
     * @var FlexibleHttpClient A http client responding to async and sync request
     */
    private $client;

    /**
     * @param HttpClient|HttpAsyncClient $client
     * @param null|int                   $reenableAfter Number of seconds after this client is reenable
     */
    public function __construct($client, $reenableAfter = null)
    {
        $this->client = new FlexibleHttpClient($client);
        $this->reenableAfter = $reenableAfter;
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request)
    {
        if ($this->isDisabled()) {
            throw new Exception\RequestException('Cannot send the request as this client has been disabled', $request);
        }

        try {
            $this->incrementRequestCount();
            $response = $this->client->sendRequest($request);
            $this->decrementRequestCount();
        } catch (Exception $e) {
            $this->disable();
            $this->decrementRequestCount();

            throw $e;
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function sendAsyncRequest(RequestInterface $request)
    {
        if ($this->isDisabled()) {
            throw new Exception\RequestException('Cannot send the request as this client has been disabled', $request);
        }

        $this->incrementRequestCount();

        return $this->client->sendAsyncRequest($request)->then(function ($response) {
            $this->decrementRequestCount();

            return $response;
        }, function ($exception) {
            $this->disable();
            $this->decrementRequestCount();

            throw $exception;
        });
    }

    /**
     * Whether this client is disabled or not.
     *
     * Will also reactivate this client if possible
     *
     * @internal
     *
     * @return bool
     */
    public function isDisabled()
    {
        $disabledAt = $this->getDisabledAt();

        if (null !== $this->reenableAfter && null !== $disabledAt) {
            // Reenable after a certain time
            $now = new \DateTime();

            if (($now->getTimestamp() - $disabledAt->getTimestamp()) >= $this->reenableAfter) {
                $this->enable();

                return false;
            }

            return true;
        }

        return null !== $disabledAt;
    }

    /**
     * Get current number of request that is send by the underlying http client.
     *
     * @internal
     *
     * @return int
     */
    public function getSendingRequestCount()
    {
        return $this->sendingRequestCount;
    }

    /**
     * Return when this client has been disabled or null if it's enabled.
     *
     * @return \DateTime|null
     */
    private function getDisabledAt()
    {
        return $this->disabledAt;
    }

    /**
     * Increment the request count.
     */
    private function incrementRequestCount()
    {
        ++$this->sendingRequestCount;
    }

    /**
     * Decrement the request count.
     */
    private function decrementRequestCount()
    {
        --$this->sendingRequestCount;
    }

    /**
     * Enable the current client.
     */
    private function enable()
    {
        $this->disabledAt = null;
    }

    /**
     * Disable the current client.
     */
    private function disable()
    {
        $this->disabledAt = new \DateTime('now');
    }
}
