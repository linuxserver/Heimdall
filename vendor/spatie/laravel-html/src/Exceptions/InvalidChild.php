<?php

namespace Spatie\Html\Exceptions;

use Exception;
use Spatie\Html\HtmlElement;

class InvalidChild extends Exception
{
    public static function childMustBeAnHtmlElementOrAString()
    {
        return new static('The given child should implement `'.HtmlElement::class.'` or be a string');
    }
}
