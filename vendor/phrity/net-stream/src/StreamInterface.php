<?php

namespace Phrity\Net;

use Psr\Http\Message\StreamInterface as PsrStreamInterface;

/**
 * StreamInterface.
 */
interface StreamInterface extends PsrStreamInterface
{
    /**
     * @return Context
     */
    public function getContext(): Context;

    /**
     * @return resource|null
     */
    public function getResource(): mixed;
}
