<?php

namespace Illuminate\Cache;

use Closure;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\InteractsWithTime;

class RateLimiter
{
    use InteractsWithTime;

    /**
     * The cache store implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * The configured limit object resolvers.
     *
     * @var array
     */
    protected $limiters = [];

    /**
     * Create a new rate limiter instance.
     *
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     * @return void
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Register a named limiter configuration.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     * @return $this
     */
    public function for(string $name, Closure $callback)
    {
        $this->limiters[$name] = $callback;

        return $this;
    }

    /**
     * Get the given named rate limiter.
     *
     * @param  string  $name
     * @return \Closure
     */
    public function limiter(string $name)
    {
        return $this->limiters[$name] ?? null;
    }

    /**
     * Attempts to execute a callback if it's not limited.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @param  \Closure  $callback
     * @param  int  $decaySeconds
     * @return mixed
     */
    public function attempt($key, $maxAttempts, Closure $callback, $decaySeconds = 60)
    {
        if ($this->tooManyAttempts($key, $maxAttempts)) {
            return false;
        }

        return tap($callback() ?: true, function () use ($key, $decaySeconds) {
            $this->hit($key, $decaySeconds);
        });
    }

    /**
     * Determine if the given key has been "accessed" too many times.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return bool
     */
    public function tooManyAttempts($key, $maxAttempts)
    {
        $key = $this->cleanRateLimiterKey($key);

        if ($this->attempts($key) >= $maxAttempts) {
            if ($this->cache->has($key.':timer')) {
                return true;
            }

            $this->resetAttempts($key);
        }

        return false;
    }

    /**
     * Increment the counter for a given key for a given decay time.
     *
     * @param  string  $key
     * @param  int  $decaySeconds
     * @return int
     */
    public function hit($key, $decaySeconds = 60)
    {
        $key = $this->cleanRateLimiterKey($key);

        $this->cache->add(
            $key.':timer', $this->availableAt($decaySeconds), $decaySeconds
        );

        $added = $this->cache->add($key, 0, $decaySeconds);

        $hits = (int) $this->cache->increment($key);

        if (! $added && $hits == 1) {
            $this->cache->put($key, 1, $decaySeconds);
        }

        return $hits;
    }

    /**
     * Get the number of attempts for the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function attempts($key)
    {
        $key = $this->cleanRateLimiterKey($key);

        return $this->cache->get($key, 0);
    }

    /**
     * Reset the number of attempts for the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function resetAttempts($key)
    {
        $key = $this->cleanRateLimiterKey($key);

        return $this->cache->forget($key);
    }

    /**
     * Get the number of retries left for the given key.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return int
     */
    public function remaining($key, $maxAttempts)
    {
        $key = $this->cleanRateLimiterKey($key);

        $attempts = $this->attempts($key);

        return $maxAttempts - $attempts;
    }

    /**
     * Get the number of retries left for the given key.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return int
     */
    public function retriesLeft($key, $maxAttempts)
    {
        return $this->remaining($key, $maxAttempts);
    }

    /**
     * Clear the hits and lockout timer for the given key.
     *
     * @param  string  $key
     * @return void
     */
    public function clear($key)
    {
        $key = $this->cleanRateLimiterKey($key);

        $this->resetAttempts($key);

        $this->cache->forget($key.':timer');
    }

    /**
     * Get the number of seconds until the "key" is accessible again.
     *
     * @param  string  $key
     * @return int
     */
    public function availableIn($key)
    {
        $key = $this->cleanRateLimiterKey($key);

        return max(0, $this->cache->get($key.':timer') - $this->currentTime());
    }

    /**
     * Clean the rate limiter key from unicode characters.
     *
     * @param  string  $key
     * @return string
     */
    public function cleanRateLimiterKey($key)
    {
        return preg_replace('/&([a-z])[a-z]+;/i', '$1', htmlentities($key));
    }
}
