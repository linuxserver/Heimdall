<?php

namespace Facade\Ignition;

use Closure;
use Facade\Ignition\Tabs\Tab;

class Ignition
{
    /** @var Closure[] */
    public static $callBeforeShowingErrorPage = [];

    /** @var array */
    public static $tabs = [];

    public static function tab(Tab $tab)
    {
        static::$tabs[] = $tab;
    }

    public static function styles(): array
    {
        return collect(static::$tabs)->flatMap(function ($tab) {
            return $tab->styles;
        })
            ->unique()
            ->toArray();
    }

    public static function scripts(): array
    {
        return collect(static::$tabs)->flatMap(function ($tab) {
            return $tab->scripts;
        })
            ->unique()
            ->toArray();
    }

    public static function registerAssets(Closure $callable)
    {
        static::$callBeforeShowingErrorPage[] = $callable;
    }
}
