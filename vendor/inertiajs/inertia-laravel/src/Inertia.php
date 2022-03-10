<?php

namespace Inertia;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void setRootView(string $name)
 * @method static void share($key, $value = null)
 * @method static array getShared(string $key = null, $default = null)
 * @method static array flushShared()
 * @method static void version($version)
 * @method static int|string getVersion()
 * @method static LazyProp lazy(callable $callback)
 * @method static Response render($component, array $props = [])
 * @method static \Illuminate\Http\Response location(string $url)
 *
 * @see \Inertia\ResponseFactory
 */
class Inertia extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ResponseFactory::class;
    }
}
