<?php

namespace Github\Exception;

use Throwable;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class MissingArgumentException extends ErrorException
{
    /**
     * @param string|array   $required
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($required, int $code = 0, Throwable $previous = null)
    {
        if (is_string($required)) {
            $required = [$required];
        }

        parent::__construct(sprintf('One or more of required ("%s") parameters is missing!', implode('", "', $required)), $code, 1, __FILE__, __LINE__, $previous);
    }
}
