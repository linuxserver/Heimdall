<?php

namespace Illuminate\Support\Facades;

use Illuminate\Contracts\Broadcasting\Factory as BroadcastingFactoryContract;

/**
 * @method static \Illuminate\Broadcasting\Broadcasters\Broadcaster channel(string $channel, callable|string  $callback, array $options = [])
 * @method static mixed auth(\Illuminate\Http\Request $request)
 * @method static \Illuminate\Contracts\Broadcasting\Broadcaster connection($name = null);
 * @method static void routes(array $attributes = null)
 * @method static \Illuminate\Broadcasting\BroadcastManager socket($request = null)
 *
 * @see \Illuminate\Contracts\Broadcasting\Factory
 */
class Broadcast extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BroadcastingFactoryContract::class;
    }
}
