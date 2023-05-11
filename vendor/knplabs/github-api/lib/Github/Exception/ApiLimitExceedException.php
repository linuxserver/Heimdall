<?php

namespace Github\Exception;

use Throwable;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class ApiLimitExceedException extends RuntimeException
{
    /** @var int */
    private $limit;
    /** @var int */
    private $reset;

    /**
     * @param int            $limit
     * @param int            $reset
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(int $limit = 5000, int $reset = 1800, int $code = 0, Throwable $previous = null)
    {
        $this->limit = (int) $limit;
        $this->reset = (int) $reset;

        parent::__construct(sprintf('You have reached GitHub hourly limit! Actual limit is: %d', $limit), $code, $previous);
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getResetTime(): int
    {
        return $this->reset;
    }
}
