<?php

namespace Phrity\Net;

/**
 * StreamContainerInterface.
 */
interface StreamContainerInterface
{
    /**
     * @return StreamInterface
     */
    public function getStream(): StreamInterface;
}
