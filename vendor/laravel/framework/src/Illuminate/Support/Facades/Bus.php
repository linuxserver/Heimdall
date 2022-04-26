<?php

namespace Illuminate\Support\Facades;

use Illuminate\Contracts\Bus\Dispatcher as BusDispatcherContract;
use Illuminate\Foundation\Bus\PendingChain;
use Illuminate\Support\Testing\Fakes\BusFake;

/**
 * @method static \Illuminate\Bus\Batch|null findBatch(string $batchId)
 * @method static \Illuminate\Bus\PendingBatch batch(array|mixed $jobs)
 * @method static \Illuminate\Contracts\Bus\Dispatcher map(array $map)
 * @method static \Illuminate\Contracts\Bus\Dispatcher pipeThrough(array $pipes)
 * @method static \Illuminate\Foundation\Bus\PendingChain chain(array $jobs)
 * @method static bool hasCommandHandler($command)
 * @method static bool|mixed getCommandHandler($command)
 * @method static mixed dispatch($command)
 * @method static mixed dispatchNow($command, $handler = null)
 * @method static mixed dispatchSync($command, $handler = null)
 * @method static void assertDispatched(string|\Closure $command, callable|int $callback = null)
 * @method static void assertDispatchedTimes(string $command, int $times = 1)
 * @method static void assertNotDispatched(string|\Closure $command, callable|int $callback = null)
 * @method static void assertDispatchedAfterResponse(string|\Closure $command, callable|int $callback = null)
 * @method static void assertDispatchedAfterResponseTimes(string $command, int $times = 1)
 * @method static void assertNotDispatchedAfterResponse(string|\Closure $command, callable $callback = null)
 * @method static void assertBatched(callable $callback)
 * @method static void assertBatchCount(int $count)
 * @method static void assertChained(array $expectedChain)
 * @method static void assertDispatchedSync(string|\Closure $command, callable $callback = null)
 * @method static void assertDispatchedSyncTimes(string $command, int $times = 1)
 * @method static void assertNotDispatchedSync(string|\Closure $command, callable $callback = null)
 * @method static void assertDispatchedWithoutChain(string|\Closure $command, callable $callback = null)
 *
 * @see \Illuminate\Contracts\Bus\Dispatcher
 */
class Bus extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @param  array|string  $jobsToFake
     * @return \Illuminate\Support\Testing\Fakes\BusFake
     */
    public static function fake($jobsToFake = [])
    {
        static::swap($fake = new BusFake(static::getFacadeRoot(), $jobsToFake));

        return $fake;
    }

    /**
     * Dispatch the given chain of jobs.
     *
     * @param  array|mixed  $jobs
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public static function dispatchChain($jobs)
    {
        $jobs = is_array($jobs) ? $jobs : func_get_args();

        return (new PendingChain(array_shift($jobs), $jobs))
                    ->dispatch();
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BusDispatcherContract::class;
    }
}
