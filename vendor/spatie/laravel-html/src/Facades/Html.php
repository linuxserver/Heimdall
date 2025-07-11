<?php

namespace Spatie\Html\Facades;

use Illuminate\Support\Facades\Facade;
use Spatie\Html\Html as HtmlBuilder;

class Html extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @see \Spatie\Html\Html
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return HtmlBuilder::class;
    }
}
