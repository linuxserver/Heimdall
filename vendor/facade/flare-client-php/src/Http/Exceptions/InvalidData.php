<?php

namespace Facade\FlareClient\Http\Exceptions;

use Facade\FlareClient\Http\Response;

class InvalidData extends BadResponseCode
{
    public static function getMessageForResponse(Response $response)
    {
        return 'Invalid data found';
    }
}
