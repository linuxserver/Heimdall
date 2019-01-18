<?php

namespace Http\Client\Common;

use Psr\Http\Message\RequestInterface;

/**
 * A client that helps you migrate from php-http/httplug 1.x to 2.x. This
 * will also help you to support PHP5 at the same time you support 2.x.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
trait VersionBridgeClient
{
    abstract protected function doSendRequest(RequestInterface $request);

    public function sendRequest(RequestInterface $request)
    {
        return $this->doSendRequest($request);
    }
}
