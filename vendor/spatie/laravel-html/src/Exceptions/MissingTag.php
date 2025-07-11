<?php

namespace Spatie\Html\Exceptions;

use Exception;

class MissingTag extends Exception
{
    public static function onClass($className): self
    {
        return new self("Class {$className} has nog `\$tag` property");
    }
}
