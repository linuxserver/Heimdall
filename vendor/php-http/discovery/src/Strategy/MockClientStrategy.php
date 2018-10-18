<?php

namespace Http\Discovery\Strategy;

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
        return (HttpClient::class === $type)
            ? [['class' => Mock::class, 'condition' => Mock::class]]
            : [];
    }
}
