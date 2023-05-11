<?php

namespace Github\Exception;

use Throwable;

class SsoRequiredException extends RuntimeException
{
    /** @var string */
    private $url;

    /**
     * @param string         $url
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $url, int $code = 0, Throwable $previous = null)
    {
        $this->url = $url;

        parent::__construct('Resource protected by organization SAML enforcement. You must grant your personal token access to this organization.', $code, $previous);
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
