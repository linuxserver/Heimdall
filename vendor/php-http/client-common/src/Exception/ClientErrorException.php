<?php

namespace Http\Client\Common\Exception;

use Http\Client\Exception\HttpException;

/**
 * Thrown when there is a client error (4xx).
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
class ClientErrorException extends HttpException
{
}
