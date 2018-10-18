<?php

namespace Github\Api;

/**
 * Get rate limits.
 *
 * @link   https://developer.github.com/v3/rate_limit/
 *
 * @author Jeff Finley <quickliketurtle@gmail.com>
 */
class RateLimit extends AbstractApi
{
    /**
     * Get rate limits.
     *
     * @return array
     */
    public function getRateLimits()
    {
        return $this->get('/rate_limit');
    }

    /**
     * Get core rate limit.
     *
     * @return int
     */
    public function getCoreLimit()
    {
        $response = $this->getRateLimits();

        return $response['resources']['core']['limit'];
    }

    /**
     * Get search rate limit.
     *
     * @return int
     */
    public function getSearchLimit()
    {
        $response = $this->getRateLimits();

        return $response['resources']['search']['limit'];
    }
}
