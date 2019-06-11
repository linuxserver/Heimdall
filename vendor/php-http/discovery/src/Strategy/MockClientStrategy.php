<?php

namespace Http\Discovery\Strategy;

use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Http\Mock\Client as Mock;

/**
 * Find the Mock client.
 *
 * @author Sam Rapaport <me@samrapdev.com>
 */
final class MockClientStrategy implements DiscoveryStrategy
{
    /**
     * {@inheritdoc}
     */
    public static function getCandidates($type)
    {
        switch ($type) {
            case HttpClient::class:
            case HttpAsyncClient::class:
                return [['class' => Mock::class, 'condition' => Mock::class]];
            default:
                return [];
       }
    }
}
