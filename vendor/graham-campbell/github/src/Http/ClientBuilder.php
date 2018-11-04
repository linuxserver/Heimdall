<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitHub.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\GitHub\Http;

use Github\HttpClient\Builder;
use GrahamCampbell\CachePlugin\CachePlugin;
use Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator;
use Psr\Cache\CacheItemPoolInterface;
use ReflectionClass;

/**
 * This is the client builder class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ClientBuilder extends Builder
{
    /**
     * Add a cache plugin to cache responses locally.
     *
     * @param \Psr\Cache\CacheItemPoolInterface $cachePool
     * @param array                             $config
     *
     * @return void
     */
    public function addCache(CacheItemPoolInterface $cachePool, array $config = [])
    {
        $this->setCachePlugin($cachePool, $config['generator'] ?? null, $config['lifetime'] ?? null);

        $this->setPropertyValue('httpClientModified', true);
    }

    /**
     * Add a cache plugin to cache responses locally.
     *
     * @param \Psr\Cache\CacheItemPoolInterface                                 $cachePool
     * @param \Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator|null $generator
     * @param int|null                                                          $lifetime
     *
     * @return void
     */
    protected function setCachePlugin(CacheItemPoolInterface $cachePool, CacheKeyGenerator $generator = null, int $lifetime = null)
    {
        $stream = $this->getPropertyValue('streamFactory');

        $this->setPropertyValue('cachePlugin', new CachePlugin($cachePool, $stream, $generator, $lifetime));
    }

    /**
     * Get the value of the given private property on the builder.
     *
     * @param string $name
     *
     * @return mixed
     */
    protected function getPropertyValue(string $name)
    {
        return static::getProperty($name)->getValue($this);
    }

    /**
     * Set the value of the given private property on the builder.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    protected function setPropertyValue(string $name, $value)
    {
        return static::getProperty($name)->setValue($this, $value);
    }

    /**
     * Get the builder reflection property for the given name.
     *
     * @param string $name
     *
     * @return \ReflectionProperty
     */
    protected static function getProperty(string $name)
    {
        $prop = (new ReflectionClass(Builder::class))->getProperty($name);

        $prop->setAccessible(true);

        return $prop;
    }
}
