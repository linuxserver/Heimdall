<?php

namespace Http\Client\Common\Plugin;

use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\Exception\RewindStreamException;
use Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator;
use Http\Client\Common\Plugin\Cache\Generator\SimpleGenerator;
use Http\Message\StreamFactory;
use Http\Promise\FulfilledPromise;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Allow for caching a response.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class CachePlugin implements Plugin
{
    /**
     * @var CacheItemPoolInterface
     */
    private $pool;

    /**
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * @var array
     */
    private $config;

    /**
     * Cache directives indicating if a response can not be cached.
     *
     * @var array
     */
    private $noCacheFlags = ['no-cache', 'private', 'no-store'];

    /**
     * @param CacheItemPoolInterface $pool
     * @param StreamFactory          $streamFactory
     * @param array                  $config        {
     *
     *     @var bool $respect_cache_headers Whether to look at the cache directives or ignore them
     *     @var int $default_ttl (seconds) If we do not respect cache headers or can't calculate a good ttl, use this
     *              value
     *     @var string $hash_algo The hashing algorithm to use when generating cache keys
     *     @var int $cache_lifetime (seconds) To support serving a previous stale response when the server answers 304
     *              we have to store the cache for a longer time than the server originally says it is valid for.
     *              We store a cache item for $cache_lifetime + max age of the response.
     *     @var array $methods list of request methods which can be cached
     *     @var array $respect_response_cache_directives list of cache directives this plugin will respect while caching responses
     *     @var CacheKeyGenerator $cache_key_generator an object to generate the cache key. Defaults to a new instance of SimpleGenerator
     * }
     */
    public function __construct(CacheItemPoolInterface $pool, StreamFactory $streamFactory, array $config = [])
    {
        $this->pool = $pool;
        $this->streamFactory = $streamFactory;

        if (isset($config['respect_cache_headers']) && isset($config['respect_response_cache_directives'])) {
            throw new \InvalidArgumentException(
                'You can\'t provide config option "respect_cache_headers" and "respect_response_cache_directives". '.
                'Use "respect_response_cache_directives" instead.'
            );
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
     * @param CacheItemPoolInterface $pool
     * @param StreamFactory          $streamFactory
     * @param array                  $config        For all possible config options see the constructor docs
     *
     * @return CachePlugin
     */
    public static function clientCache(CacheItemPoolInterface $pool, StreamFactory $streamFactory, array $config = [])
    {
        // Allow caching of private requests
        if (isset($config['respect_response_cache_directives'])) {
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
     * @param CacheItemPoolInterface $pool
     * @param StreamFactory          $streamFactory
     * @param array                  $config        For all possible config options see the constructor docs
     *
     * @return CachePlugin
     */
    public static function serverCache(CacheItemPoolInterface $pool, StreamFactory $streamFactory, array $config = [])
    {
        return new self($pool, $streamFactory, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $method = strtoupper($request->getMethod());
        // if the request not is cachable, move to $next
        if (!in_array($method, $this->config['methods'])) {
            return $next($request);
        }

        // If we can cache the request
        $key = $this->createCacheKey($request);
        $cacheItem = $this->pool->getItem($key);

        if ($cacheItem->isHit()) {
            $data = $cacheItem->get();
            // The array_key_exists() is to be removed in 2.0.
            if (array_key_exists('expiresAt', $data) && (null === $data['expiresAt'] || time() < $data['expiresAt'])) {
                // This item is still valid according to previous cache headers
                return new FulfilledPromise($this->createResponseFromCacheItem($cacheItem));
            }

            // Add headers to ask the server if this cache is still valid
            if ($modifiedSinceValue = $this->getModifiedSinceHeaderValue($cacheItem)) {
                $request = $request->withHeader('If-Modified-Since', $modifiedSinceValue);
            }

            if ($etag = $this->getETag($cacheItem)) {
                $request = $request->withHeader('If-None-Match', $etag);
            }
        }

        return $next($request)->then(function (ResponseInterface $response) use ($cacheItem) {
            if (304 === $response->getStatusCode()) {
                if (!$cacheItem->isHit()) {
                    /*
                     * We do not have the item in cache. This plugin did not add If-Modified-Since
                     * or If-None-Match headers. Return the response from server.
                     */
                    return $response;
                }

                // The cached response we have is still valid
                $data = $cacheItem->get();
                $maxAge = $this->getMaxAge($response);
                $data['expiresAt'] = $this->calculateResponseExpiresAt($maxAge);
                $cacheItem->set($data)->expiresAfter($this->calculateCacheItemExpiresAfter($maxAge));
                $this->pool->save($cacheItem);

                return $this->createResponseFromCacheItem($cacheItem);
            }

            if ($this->isCacheable($response)) {
                $bodyStream = $response->getBody();
                $body = $bodyStream->__toString();
                if ($bodyStream->isSeekable()) {
                    $bodyStream->rewind();
                } else {
                    $response = $response->withBody($this->streamFactory->createStream($body));
                }

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
            }

            return $response;
        });
    }

    /**
     * Calculate the timestamp when this cache item should be dropped from the cache. The lowest value that can be
     * returned is $maxAge.
     *
     * @param int|null $maxAge
     *
     * @return int|null Unix system time passed to the PSR-6 cache
     */
    private function calculateCacheItemExpiresAfter($maxAge)
    {
        if (null === $this->config['cache_lifetime'] && null === $maxAge) {
            return;
        }

        return $this->config['cache_lifetime'] + $maxAge;
    }

    /**
     * Calculate the timestamp when a response expires. After that timestamp, we need to send a
     * If-Modified-Since / If-None-Match request to validate the response.
     *
     * @param int|null $maxAge
     *
     * @return int|null Unix system time. A null value means that the response expires when the cache item expires
     */
    private function calculateResponseExpiresAt($maxAge)
    {
        if (null === $maxAge) {
            return;
        }

        return time() + $maxAge;
    }

    /**
     * Verify that we can cache this response.
     *
     * @param ResponseInterface $response
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
     * Get the value of a parameter in the cache control header.
     *
     * @param ResponseInterface $response
     * @param string            $name     The field of Cache-Control to fetch
     *
     * @return bool|string The value of the directive, true if directive without value, false if directive not present
     */
    private function getCacheControlDirective(ResponseInterface $response, $name)
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

    /**
     * @param RequestInterface $request
     *
     * @return string
     */
    private function createCacheKey(RequestInterface $request)
    {
        $key = $this->config['cache_key_generator']->generate($request);

        return hash($this->config['hash_algo'], $key);
    }

    /**
     * Get a ttl in seconds. It could return null if we do not respect cache headers and got no defaultTtl.
     *
     * @param ResponseInterface $response
     *
     * @return int|null
     */
    private function getMaxAge(ResponseInterface $response)
    {
        if (!in_array('max-age', $this->config['respect_response_cache_directives'], true)) {
            return $this->config['default_ttl'];
        }

        // check for max age in the Cache-Control header
        $maxAge = $this->getCacheControlDirective($response, 'max-age');
        if (!is_bool($maxAge)) {
            $ageHeaders = $response->getHeader('Age');
            foreach ($ageHeaders as $age) {
                return $maxAge - ((int) $age);
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
     *
     * @param OptionsResolver $resolver
     */
    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'cache_lifetime' => 86400 * 30, // 30 days
            'default_ttl' => 0,
            //Deprecated as of v1.3, to be removed in v2.0. Use respect_response_cache_directives instead
            'respect_cache_headers' => null,
            'hash_algo' => 'sha1',
            'methods' => ['GET', 'HEAD'],
            'respect_response_cache_directives' => ['no-cache', 'private', 'max-age', 'no-store'],
            'cache_key_generator' => null,
        ]);

        $resolver->setAllowedTypes('cache_lifetime', ['int', 'null']);
        $resolver->setAllowedTypes('default_ttl', ['int', 'null']);
        $resolver->setAllowedTypes('respect_cache_headers', ['bool', 'null']);
        $resolver->setAllowedTypes('methods', 'array');
        $resolver->setAllowedTypes('cache_key_generator', ['null', 'Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator']);
        $resolver->setAllowedValues('hash_algo', hash_algos());
        $resolver->setAllowedValues('methods', function ($value) {
            /* RFC7230 sections 3.1.1 and 3.2.6 except limited to uppercase characters. */
            $matches = preg_grep('/[^A-Z0-9!#$%&\'*+\-.^_`|~]/', $value);

            return empty($matches);
        });

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

    /**
     * @param CacheItemInterface $cacheItem
     *
     * @return ResponseInterface
     */
    private function createResponseFromCacheItem(CacheItemInterface $cacheItem)
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

        $response = $response->withBody($stream);

        return $response;
    }

    /**
     * Get the value of the "If-Modified-Since" header.
     *
     * @param CacheItemInterface $cacheItem
     *
     * @return string|null
     */
    private function getModifiedSinceHeaderValue(CacheItemInterface $cacheItem)
    {
        $data = $cacheItem->get();
        // The isset() is to be removed in 2.0.
        if (!isset($data['createdAt'])) {
            return;
        }

        $modified = new \DateTime('@'.$data['createdAt']);
        $modified->setTimezone(new \DateTimeZone('GMT'));

        return sprintf('%s GMT', $modified->format('l, d-M-y H:i:s'));
    }

    /**
     * Get the ETag from the cached response.
     *
     * @param CacheItemInterface $cacheItem
     *
     * @return string|null
     */
    private function getETag(CacheItemInterface $cacheItem)
    {
        $data = $cacheItem->get();
        // The isset() is to be removed in 2.0.
        if (!isset($data['etag'])) {
            return;
        }

        foreach ($data['etag'] as $etag) {
            if (!empty($etag)) {
                return $etag;
            }
        }
    }
}
