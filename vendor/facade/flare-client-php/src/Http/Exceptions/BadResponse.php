<?php

namespace Facade\FlareClient\Http\Exceptions;

use Exception;
use Facade\FlareClient\Http\Response;

class BadResponse extends Exception
{
    /** @var \Facade\FlareClient\Http\Response */
    public $response;

    public static function createForResponse(Response $response)
    {
        $exception = new static("Could not perform request because: {$response->getError()}");

        $exception->response = $response;

        return $exception;
    }
}
