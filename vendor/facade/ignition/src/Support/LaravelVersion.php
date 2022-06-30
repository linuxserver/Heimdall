<?php

namespace Facade\Ignition\Support;

class LaravelVersion
{
    public static function major()
    {
        return substr(app()->version(), 0, 1);
    }
}
