<?php

namespace Http\Client\Common;

use Http\Client\Exception;
use Http\Promise\Promise;
use Psr\Http\Message\ResponseInterface;

/**
 * A deferred allow to return a promise which has not been resolved yet.
 */
class Deferred implements Promise
{
    private $value;

    private $failure;

    private $state;

    private $waitCallback;

    private $onFulfilledCallbacks;

    private $onRejectedCallbacks;

    public function __construct(callable $waitCallback)
    {
        $this->waitCallback = $waitCallback;
        $this->state = Promise::PENDING;
        $this->onFulfilledCallbacks = [];
        $this->onRejectedCallbacks = [];
    }

    /**
     * {@inheritdoc}
     */
    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        $deferred = new self($this->waitCallback);

        $this->onFulfilledCallbacks[] = function (ResponseInterface $response) use ($onFulfilled, $deferred) {
            try {
                if (null !== $onFulfilled) {
                    $response = $onFulfilled($response);
                }
                $deferred->resolve($response);
            } catch (Exception $exception) {
                $deferred->reject($exception);
            }
        };

        $this->onRejectedCallbacks[] = function (Exception $exception) use ($onRejected, $deferred) {
            try {
                if (null !== $onRejected) {
                    $response = $onRejected($exception);
                    $deferred->resolve($response);

                    return;
                }
                $deferred->reject($exception);
            } catch (Exception $newException) {
                $deferred->reject($newException);
            }
        };

        return $deferred;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Resolve this deferred with a Response.
     */
    public function resolve(ResponseInterface $response)
    {
        if (self::PENDING !== $this->state) {
            return;
        }

        $this->value = $response;
        $this->state = self::FULFILLED;

        foreach ($this->onFulfilledCallbacks as $onFulfilledCallback) {
            $onFulfilledCallback($response);
        }
    }

    /**
     * Reject this deferred with an Exception.
     */
    public function reject(Exception $exception)
    {
        if (self::PENDING !== $this->state) {
            return;
        }

        $this->failure = $exception;
        $this->state = self::REJECTED;

        foreach ($this->onRejectedCallbacks as $onRejectedCallback) {
            $onRejectedCallback($exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function wait($unwrap = true)
    {
        if (self::PENDING === $this->state) {
            $callback = $this->waitCallback;
            $callback();
        }

        if (!$unwrap) {
            return;
        }

        if (self::FULFILLED === $this->state) {
            return $this->value;
        }

        throw $this->failure;
    }
}
