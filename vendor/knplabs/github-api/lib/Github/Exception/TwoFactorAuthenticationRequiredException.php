<?php

namespace Github\Exception;

class TwoFactorAuthenticationRequiredException extends RuntimeException
{
    private $type;

    public function __construct($type, $code = 0, $previous = null)
    {
        $this->type = $type;
        parent::__construct('Two factor authentication is enabled on this account', $code, $previous);
    }

    public function getType()
    {
        return $this->type;
    }
}
