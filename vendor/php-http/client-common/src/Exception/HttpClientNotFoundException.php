<?php

namespace Http\Client\Common\Exception;

use Http\Client\Exception\TransferException;

/**
 * Thrown when a http client cannot be chosen in a pool.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class HttpClientNotFoundException extends TransferException
{
}
