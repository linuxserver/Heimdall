<?php

namespace Github\Api\RateLimit;

/**
 * Represents the data block for a GitHub rate limit response, grouped by a name.
 */
class RateLimitResource
{
    /** @var string */
    private $name;

    /** @var int */
    private $limit;

    /** @var int */
    private $reset;

    /** @var int */
    private $remaining;

    /**
     * @param string $name
     * @param array  $data
     */
    public function __construct($name, array $data)
    {
        $this->name = $name;
        $this->limit = $data['limit'];
        $this->remaining = $data['remaining'];
        $this->reset = $data['reset'];
    }

    /**
     * The name of the Limit, e.g. "core", "graphql", "search".
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * The rate limit amount.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Number of requests remaining in time period before hitting the rate limit.
     *
     * @return int
     */
    public function getRemaining()
    {
        return $this->remaining;
    }

    /**
     * Timestamp for when the rate limit will be reset.
     *
     * @return int
     */
    public function getReset()
    {
        return $this->reset;
    }
}
