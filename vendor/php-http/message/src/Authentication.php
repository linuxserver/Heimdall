<?php

namespace Http\Message;

use Psr\Http\Message\RequestInterface;

/**
 * Authenticate a PSR-7 Request.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
interface Authentication
{
    /**
     * Authenticates a request.
     *
     * @param RequestInterface $request
     *
     * @return RequestInterface
     */
    public function authenticate(RequestInterface $request);
}
