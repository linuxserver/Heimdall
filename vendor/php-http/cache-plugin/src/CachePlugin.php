<?php

namespace Http\Client\Common\Plugin;

use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\Exception\RewindStreamException;
use Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator;
use Http\Client\Common\Plugin\Cache\Generator\SimpleGenerator;
use Http\Client\Common\Plugin\Cache\Listener\CacheListener;
use Http\Message\StreamFactory;
use Http\Promise\FulfilledPromise;
use Http\Promise\Promise;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Allow for caching a response with a PSR-6 compatible caching engine.
 *
 * It can follow the RFC-7234 caching specification or use a fixed cache lifetime.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class CachePlugin implements Plugin
{
    use VersionBridgePlugin;

    /**
     * @var CacheItemPoolInterface
     */
    private $pool;

    /**
     * @var StreamFactory|StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * @var mixed[]
     */
    private $config;

    /**
     * Cache directives indicating if a response can not be cached.
     *
     * @var string[]
     */
    private $noCacheFlags = ['no-cache', 'private', 'no-store'];

    /**
     * @param StreamFactory|StreamFactoryInterface $streamFactory
     * @param mixed[]                              $config
     *
     *     bool respect_cache_headers: Whether to look at the cache directives or ignore them
     *     int default_ttl: (seconds) If we do not respect cache headers or can't calculate a good ttl, use this value
     *     string hash_algo: The hashing algorithm to use when generating cache keys
     *     int|null cache_lifetime: (seconds) To support serving a previous stale response when the server answers 304
     *              we have to store the cache for a longer time than the server originally says it is valid for.
     *              We store a cache item for $cache_lifetime + max age of the response.
     *     string[] methods: list of request methods which can be cached
     *     string[] blacklisted_paths: list of regex for URLs explicitly not to be cached
     *     string[] respect_response_cache_directives: list of cache directives this plugin will respect while caching responses
     *     CacheKeyGenerator cache_key_generator: an object to generate the cache key. Defaults to a new instance of SimpleGenerator
     *     CacheListener[] cache_listeners: an array of objects to act on the response based on the results of the cache check.
     *              Defaults to an empty array
     * }
     */
    public function __construct(CacheItemPoolInterface $pool, $streamFactory, array $config = [])
    {
        if (!($streamFactory instanceof StreamFactory) && !($streamFactory instanceof StreamFactoryInterface)) {
            throw new \TypeError(\sprintf('Argument 2 passed to %s::__construct() must be of type %s|%s, %s given.', self::class, StreamFactory::class, StreamFactoryInterface::class, \is_object($streamFactory) ? \get_class($streamFactory) : \gettype($streamFactory)));
        }

        $this->pool = $pool;
        $this->streamFactory = $streamFactory;

        if (\array_key_exists('respect_cache_headers', $config) && \array_key_exists('respect_response_cache_directives', $config)) {
            throw new \InvalidArgumentException('You can\'t provide config option "respect_cache_headers" and "respect_response_cache_directives". Use "respect_response_cache_directives" instead.');
        }

        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->config = $optionsResolver->resolve($config);

        if (null === $this->config['cache_key_generator']) {
            $this->config['cache_key_generator'] = new SimpleGenerator();
        }
    }

    /**
     * This method will setup the cachePlugin in client cache mode. When using the client cache mode the plugin will
     * cache responses with `private` cache directive.
     *
     * @param StreamFactory|StreamFactoryInterface $streamFactory
     * @param mixed[]                              $config        For all possible config options see the constructor docs
     *
     * @return CachePlugin
     */
    public static function clientCache(CacheItemPoolInterface $pool, $streamFactory, array $config = [])
    {
        // Allow caching of private requests
        if (\array_key_exists('respect_response_cache_directives', $config)) {
            $config['respect_response_cache_directives'][] = 'no-cache';
            $config['respect_response_cache_directives'][] = 'max-age';
            $config['respect_response_cache_directives'] = array_unique($config['respect_response_cache_directives']);
        } else {
            $config['respect_response_cache_directives'] = ['no-cache', 'max-age'];
        }

        return new self($pool, $streamFactory, $config);
    }

    /**
     * This method will setup the cachePlugin in server cache mode. This is the default caching behavior it refuses to
     * cache responses with the `private`or `no-cache` directives.
     *
     * @param StreamFactory|StreamFactoryInterface $streamFactory
     * @param mixed[]                              $config        For all possible config options see the constructor docs
     *
     * @return CachePlugin
     */
    public static function serverCache(CacheItemPoolInterface $pool, $streamFactory, array $config = [])
    {
        return new self($pool, $streamFactory, $config);
    }

    /**
     * {@inheritdoc}
     *
     * @return Promise Resolves a PSR-7 Response or fails with an Http\Client\Exception (The same as HttpAsyncClient)
     */
    protected function doHandleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $method = strtoupper($request->getMethod());
        // if the request not is cachable, move to $next
        if (!in_array($method, $this->config['methods'])) {
            return $next($request)->then(function (ResponseInterface $response) use ($request) {
                $response = $this->handleCacheListeners($request, $response, false, null);

                return $response;
            });
        }

        // If we can cache the request
        $key = $this->createCacheKey($request);
        $cacheItem = $this->pool->getItem($key);

        if ($cacheItem->isHit()) {
            $data = $cacheItem->get();
            if (is_array($data)) {
                // The array_key_exists() is to be removed in 2.0.
                if (array_key_exists('expiresAt', $data) && (null === $data['expiresAt'] || time() < $data['expiresAt'])) {
                    // This item is still valid according to previous cache headers
                    $response = $this->createResponseFromCacheItem($cacheItem);
                    $response = $this->handleCacheListeners($request, $response, true, $cacheItem);

                    return new FulfilledPromise($response);
                }

                // Add headers to ask the server if this cache is still valid
                if ($modifiedSinceValue = $this->getModifiedSinceHeaderValue($cacheItem)) {
                    $request = $request->withHeader('If-Modified-Since', $modifiedSinceValue);
                }

                if ($etag = $this->getETag($cacheItem)) {
                    $request = $request->withHeader('If-None-Match', $etag);
                }
            }
        }

        return $next($request)->then(function (ResponseInterface $response) use ($request, $cacheItem) {
            if (304 === $response->getStatusCode()) {
                if (!$cacheItem->isHit()) {
                    /*
                     * We do not have the item in cache. This plugin did not add If-Modified-Since
                     * or If-None-Match headers. Return the response from server.
                     */
                    return $this->handleCacheListeners($request, $response, false, $cacheItem);
                }

                // The cached response we have is still valid
                $data = $cacheItem->get();
                $maxAge = $this->getMaxAge($response);
                $data['expiresAt'] = $this->calculateResponseExpiresAt($maxAge);
                $cacheItem->set($data)->expiresAfter($this->calculateCacheItemExpiresAfter($maxAge));
                $this->pool->save($cacheItem);

                return $this->handleCacheListeners($request, $this->createResponseFromCacheItem($cacheItem), true, $cacheItem);
            }

            if ($this->isCacheable($response) && $this->isCacheableRequest($request)) {
                /* The PSR-7 response body is a stream. We can't expect that the response implements Serializable and handles the body.
                 * Therefore we store the body separately and detach the stream to avoid attempting to serialize a resource.
                .* Our implementation still makes the assumption that the response object apart from the body can be serialized and deserialized.
                 */
                $bodyStream = $response->getBody();
                $body = $bodyStream->__toString();
                $bodyStream->detach();

                $maxAge = $this->getMaxAge($response);
                $cacheItem
                    ->expiresAfter($this->calculateCacheItemExpiresAfter($maxAge))
                    ->set([
                        'response' => $response,
                        'body' => $body,
                        'expiresAt' => $this->calculateResponseExpiresAt($maxAge),
                        'createdAt' => time(),
                        'etag' => $response->getHeader('ETag'),
                    ]);
                $this->pool->save($cacheItem);

                $bodyStream = $this->streamFactory->createStream($body);
                if ($bodyStream->isSeekable()) {
                    $bodyStream->rewind();
                }

                $response = $response->withBody($bodyStream);
            }

            return $this->handleCacheListeners($request, $response, false, $cacheItem);
        });
    }

    /**
     * Calculate the timestamp when this cache item should be dropped from the cache. The lowest value that can be
     * returned is $maxAge.
     *
     * @return int|null Unix system time passed to the PSR-6 cache
     */
    private function calculateCacheItemExpiresAfter(?int $maxAge): ?int
    {
        if (null === $this->config['cache_lifetime'] && null === $maxAge) {
            return null;
        }

        return ($this->config['cache_lifetime'] ?: 0) + ($maxAge ?: 0);
    }

    /**
     * Calculate the timestamp when a response expires. After that timestamp, we need to send a
     * If-Modified-Since / If-None-Match request to validate the response.
     *
     * @return int|null Unix system time. A null value means that the response expires when the cache item expires
     */
    private function calculateResponseExpiresAt(?int $maxAge): ?int
    {
        if (null === $maxAge) {
            return null;
        }

        return time() + $maxAge;
    }

    /**
     * Verify that we can cache this response.
     *
     * @return bool
     */
    protected function isCacheable(ResponseInterface $response)
    {
        if (!in_array($response->getStatusCode(), [200, 203, 300, 301, 302, 404, 410])) {
            return false;
        }

        $nocacheDirectives = array_intersect($this->config['respect_response_cache_directives'], $this->noCacheFlags);
        foreach ($nocacheDirectives as $nocacheDirective) {
            if ($this->getCacheControlDirective($response, $nocacheDirective)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verify that we can cache this request.
     */
    private function isCacheableRequest(RequestInterface $request): bool
    {
        $uri = $request->getUri()->__toString();
        foreach ($this->config['blacklisted_paths'] as $regex) {
            if (1 === preg_match($regex, $uri)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the value of a parameter in the cache control header.
     *
     * @param string $name The field of Cache-Control to fetch
     *
     * @return bool|string The value of the directive, true if directive without value, false if directive not present
     */
    private function getCacheControlDirective(ResponseInterface $response, string $name)
    {
        $headers = $response->getHeader('Cache-Control');
        foreach ($headers as $header) {
            if (preg_match(sprintf('|%s=?([0-9]+)?|i', $name), $header, $matches)) {
                // return the value for $name if it exists
                if (isset($matches[1])) {
                    return $matches[1];
                }

                return true;
            }
        }

        return false;
    }

    private function createCacheKey(RequestInterface $request): string
    {
        $key = $this->config['cache_key_generator']->generate($request);

        return hash($this->config['hash_algo'], $key);
    }

    /**
     * Get a ttl in seconds.
     *
     * Returns null if we do not respect cache headers and got no defaultTtl.
     */
    private function getMaxAge(ResponseInterface $response): ?int
    {
        if (!in_array('max-age', $this->config['respect_response_cache_directives'], true)) {
            return $this->config['default_ttl'];
        }

        // check for max age in the Cache-Control header
        $maxAge = $this->getCacheControlDirective($response, 'max-age');
        if (!is_bool($maxAge)) {
            $ageHeaders = $response->getHeader('Age');
            foreach ($ageHeaders as $age) {
                return ((int) $maxAge) - ((int) $age);
            }

            return (int) $maxAge;
        }

        // check for ttl in the Expires header
        $headers = $response->getHeader('Expires');
        foreach ($headers as $header) {
            return (new \DateTime($header))->getTimestamp() - (new \DateTime())->getTimestamp();
        }

        return $this->config['default_ttl'];
    }

    /**
     * Configure an options resolver.
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'cache_lifetime' => 86400 * 30, // 30 days
            'default_ttl' => 0,
            // Deprecated as of v1.3, to be removed in v2.0. Use respect_response_cache_directives instead
            'respect_cache_headers' => null,
            'hash_algo' => 'sha1',
            'methods' => ['GET', 'HEAD'],
            'respect_response_cache_directives' => ['no-cache', 'private', 'max-age', 'no-store'],
            'cache_key_generator' => null,
            'cache_listeners' => [],
            'blacklisted_paths' => [],
        ]);

        $resolver->setAllowedTypes('cache_lifetime', ['int', 'null']);
        $resolver->setAllowedTypes('default_ttl', ['int', 'null']);
        $resolver->setAllowedTypes('respect_cache_headers', ['bool', 'null']);
        $resolver->setAllowedTypes('methods', 'array');
        $resolver->setAllowedTypes('cache_key_generator', ['null', CacheKeyGenerator::class]);
        $resolver->setAllowedTypes('blacklisted_paths', 'array');
        $resolver->setAllowedValues('hash_algo', hash_algos());
        $resolver->setAllowedValues('methods', function ($value) {
            /* RFC7230 sections 3.1.1 and 3.2.6 except limited to uppercase characters. */
            $matches = preg_grep('/[^A-Z0-9!#$%&\'*+\-.^_`|~]/', $value);

            return empty($matches);
        });
        $resolver->setAllowedTypes('cache_listeners', ['array']);

        $resolver->setNormalizer('respect_cache_headers', function (Options $options, $value) {
            if (null !== $value) {
                @trigger_error('The option "respect_cache_headers" is deprecated since version 1.3 and will be removed in 2.0. Use "respect_response_cache_directives" instead.', E_USER_DEPRECATED);
            }

            return null === $value ? true : $value;
        });

        $resolver->setNormalizer('respect_response_cache_directives', function (Options $options, $value) {
            if (false === $options['respect_cache_headers']) {
                return [];
            }

            return $value;
        });
    }

    private function createResponseFromCacheItem(CacheItemInterface $cacheItem): ResponseInterface
    {
        $data = $cacheItem->get();

        /** @var ResponseInterface $response */
        $response = $data['response'];
        $stream = $this->streamFactory->createStream($data['body']);

        try {
            $stream->rewind();
        } catch (\Exception $e) {
            throw new RewindStreamException('Cannot rewind stream.', 0, $e);
        }

        return $response->withBody($stream);
    }

    /**
     * Get the value for the "If-Modified-Since" header.
     */
    private function getModifiedSinceHeaderValue(CacheItemInterface $cacheItem): ?string
    {
        $data = $cacheItem->get();
        // The isset() is to be removed in 2.0.
        if (!isset($data['createdAt'])) {
            return null;
        }

        $modified = new \DateTime('@'.$data['createdAt']);
        $modified->setTimezone(new \DateTimeZone('GMT'));

        return sprintf('%s GMT', $modified->format('l, d-M-y H:i:s'));
    }

    /**
     * Get the ETag from the cached response.
     */
    private function getETag(CacheItemInterface $cacheItem): ?string
    {
        $data = $cacheItem->get();
        // The isset() is to be removed in 2.0.
        if (!isset($data['etag'])) {
            return null;
        }

        foreach ($data['etag'] as $etag) {
            if (!empty($etag)) {
                return $etag;
            }
        }

        return null;
    }

    /**
     * Call the registered cache listeners.
     */
    private function handleCacheListeners(RequestInterface $request, ResponseInterface $response, bool $cacheHit, ?CacheItemInterface $cacheItem): ResponseInterface
    {
        foreach ($this->config['cache_listeners'] as $cacheListener) {
            $response = $cacheListener->onCacheResponse($request, $response, $cacheHit, $cacheItem);
        }

        return $response;
    }
}
