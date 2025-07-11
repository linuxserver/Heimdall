<?php

namespace Spatie\ErrorSolutions\Support\Laravel;

class LaravelVersion
{
    public static function major(): string
    {
        return explode('.', app()->version())[0];
    }
}
