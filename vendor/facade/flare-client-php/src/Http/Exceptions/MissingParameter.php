<?php

namespace Facade\FlareClient\Http\Exceptions;

use Exception;

class MissingParameter extends Exception
{
    public static function create(string $parameterName)
    {
        return new static("`$parameterName` is a required parameter");
    }
}
