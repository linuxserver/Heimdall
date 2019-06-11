<?php

namespace Github\Api;

use Github\Api\RateLimit\RateLimitResource;

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
     * @var RateLimitResource[]
     */
    protected $resources = [];

    /**
     * Get rate limits data in an array.
     *
     * @deprecated since 2.11.0 Use `->getResources()` instead
     *
     * @return array
     */
    public function getRateLimits()
    {
        return $this->fetchLimits();
    }

    /**
     * Gets the rate limit resource objects.
     *
     * @return RateLimitResource[]
     */
    public function getResources()
    {
        $this->fetchLimits();

        return $this->resources;
    }

    /**
     * Returns a rate limit resource object by the given name.
     *
     * @param string $name
     *
     * @return RateLimitResource|false
     */
    public function getResource($name)
    {
        // Fetch once per instance
        if (empty($this->resources)) {
            $this->fetchLimits();
        }

        if (!isset($this->resources[$name])) {
            return false;
        }

        return $this->resources[$name];
    }

    /**
     * Returns the data directly from the GitHub API endpoint.
     *
     * @return array
     */
    protected function fetchLimits()
    {
        $result = $this->get('/rate_limit') ?: [];

        // Assemble Limit instances
        foreach ($result['resources'] as $resourceName => $resource) {
            $this->resources[$resourceName] = new RateLimitResource($resourceName, $resource);
        }

        return $result;
    }

    /**
     * Get core rate limit.
     *
     * @deprecated since 2.11.0 Use `->getResource('core')->getLimit()` instead
     *
     * @return int
     */
    public function getCoreLimit()
    {
        return $this->getResource('core')->getLimit();
    }

    /**
     * Get search rate limit.
     *
     * @deprecated since 2.11.0 Use `->getResource('core')->getLimit()` instead
     *
     * @return int
     */
    public function getSearchLimit()
    {
        return $this->getResource('search')->getLimit();
    }
}
