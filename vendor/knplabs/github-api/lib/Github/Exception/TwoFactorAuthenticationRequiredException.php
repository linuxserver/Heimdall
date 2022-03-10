<?php

namespace Github\Exception;

use Throwable;

class TwoFactorAuthenticationRequiredException extends RuntimeException
{
    /** @var string */
    private $type;

    /**
     * @param string         $type
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $type, int $code = 0, Throwable $previous = null)
    {
        $this->type = $type;
        parent::__construct('Two factor authentication is enabled on this account', $code, $previous);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
