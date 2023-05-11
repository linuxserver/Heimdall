<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Adapter;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Cache\PruneableInterface;
use Symfony\Component\Cache\ResettableInterface;
use Symfony\Component\Cache\Traits\ContractsTrait;
use Symfony\Component\Cache\Traits\ProxyTrait;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class TagAwareAdapter implements TagAwareAdapterInterface, TagAwareCacheInterface, PruneableInterface, ResettableInterface, LoggerAwareInterface
{
    use ContractsTrait;
    use LoggerAwareTrait;
    use ProxyTrait;

    public const TAGS_PREFIX = "\0tags\0";

    private $deferred = [];
    private $tags;
    private $knownTagVersions = [];
    private $knownTagVersionsTtl;

    private static $createCacheItem;
    private static $setCacheItemTags;
    private static $getTagsByKey;
    private static $saveTags;

    public function __construct(AdapterInterface $itemsPool, AdapterInterface $tagsPool = null, float $knownTagVersionsTtl = 0.15)
    {
        $this->pool = $itemsPool;
        $this->tags = $tagsPool ?: $itemsPool;
        $this->knownTagVersionsTtl = $knownTagVersionsTtl;
        self::$createCacheItem ?? self::$createCacheItem = \Closure::bind(
            static function ($key, $value, CacheItem $protoItem) {
                $item = new CacheItem();
                $item->key = $key;
                $item->value = $value;
                $item->expiry = $protoItem->expiry;
                $item->poolHash = $protoItem->poolHash;

                return $item;
            },
            null,
            CacheItem::class
        );
        self::$setCacheItemTags ?? self::$setCacheItemTags = \Closure::bind(
            static function (CacheItem $item, $key, array &$itemTags) {
                $item->isTaggable = true;
                if (!$item->isHit) {
                    return $item;
                }
                if (isset($itemTags[$key])) {
                    foreach ($itemTags[$key] as $tag => $version) {
                        $item->metadata[CacheItem::METADATA_TAGS][$tag] = $tag;
                    }
                    unset($itemTags[$key]);
                } else {
                    $item->value = null;
                    $item->isHit = false;
                }

                return $item;
            },
            null,
            CacheItem::class
        );
        self::$getTagsByKey ?? self::$getTagsByKey = \Closure::bind(
            static function ($deferred) {
                $tagsByKey = [];
                foreach ($deferred as $key => $item) {
                    $tagsByKey[$key] = $item->newMetadata[CacheItem::METADATA_TAGS] ?? [];
                    $item->metadata = $item->newMetadata;
                }

                return $tagsByKey;
            },
            null,
            CacheItem::class
        );
        self::$saveTags ?? self::$saveTags = \Closure::bind(
            static function (AdapterInterface $tagsAdapter, array $tags) {
                ksort($tags);

                foreach ($tags as $v) {
                    $v->expiry = 0;
                    $tagsAdapter->saveDeferred($v);
                }

                return $tagsAdapter->commit();
            },
            null,
            CacheItem::class
        );
    }

    /**
     * {@inheritdoc}
     */
    public function invalidateTags(array $tags)
    {
        $ids = [];
        foreach ($tags as $tag) {
            \assert('' !== CacheItem::validateKey($tag));
            unset($this->knownTagVersions[$tag]);
            $ids[] = $tag.static::TAGS_PREFIX;
        }

        return !$tags || $this->tags->deleteItems($ids);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function hasItem($key)
    {
        if (\is_string($key) && isset($this->deferred[$key])) {
            $this->commit();
        }

        if (!$this->pool->hasItem($key)) {
            return false;
        }

        $itemTags = $this->pool->getItem(static::TAGS_PREFIX.$key);

        if (!$itemTags->isHit()) {
            return false;
        }

        if (!$itemTags = $itemTags->get()) {
            return true;
        }

        foreach ($this->getTagVersions([$itemTags]) as $tag => $version) {
            if ($itemTags[$tag] !== $version) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        foreach ($this->getItems([$key]) as $item) {
            return $item;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        $tagKeys = [];
        $commit = false;

        foreach ($keys as $key) {
            if ('' !== $key && \is_string($key)) {
                $commit = $commit || isset($this->deferred[$key]);
                $key = static::TAGS_PREFIX.$key;
                $tagKeys[$key] = $key;
            }
        }

        if ($commit) {
            $this->commit();
        }

        try {
            $items = $this->pool->getItems($tagKeys + $keys);
        } catch (InvalidArgumentException $e) {
            $this->pool->getItems($keys); // Should throw an exception

            throw $e;
        }

        return $this->generateItems($items, $tagKeys);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function clear(string $prefix = '')
    {
        if ('' !== $prefix) {
            foreach ($this->deferred as $key => $item) {
                if (str_starts_with($key, $prefix)) {
                    unset($this->deferred[$key]);
                }
            }
        } else {
            $this->deferred = [];
        }

        if ($this->pool instanceof AdapterInterface) {
            return $this->pool->clear($prefix);
        }

        return $this->pool->clear();
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteItem($key)
    {
        return $this->deleteItems([$key]);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            if ('' !== $key && \is_string($key)) {
                $keys[] = static::TAGS_PREFIX.$key;
            }
        }

        return $this->pool->deleteItems($keys);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function save(CacheItemInterface $item)
    {
        if (!$item instanceof CacheItem) {
            return false;
        }
        $this->deferred[$item->getKey()] = $item;

        return $this->commit();
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        if (!$item instanceof CacheItem) {
            return false;
        }
        $this->deferred[$item->getKey()] = $item;

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function commit()
    {
        if (!$this->deferred) {
            return true;
        }

        $ok = true;
        foreach ($this->deferred as $key => $item) {
            if (!$this->pool->saveDeferred($item)) {
                unset($this->deferred[$key]);
                $ok = false;
            }
        }

        $items = $this->deferred;
        $tagsByKey = (self::$getTagsByKey)($items);
        $this->deferred = [];

        $tagVersions = $this->getTagVersions($tagsByKey);
        $f = self::$createCacheItem;

        foreach ($tagsByKey as $key => $tags) {
            $this->pool->saveDeferred($f(static::TAGS_PREFIX.$key, array_intersect_key($tagVersions, $tags), $items[$key]));
        }

        return $this->pool->commit() && $ok;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        throw new \BadMethodCallException('Cannot serialize '.__CLASS__);
    }

    public function __wakeup()
    {
        throw new \BadMethodCallException('Cannot unserialize '.__CLASS__);
    }

    public function __destruct()
    {
        $this->commit();
    }

    private function generateItems(iterable $items, array $tagKeys): \Generator
    {
        $bufferedItems = $itemTags = [];
        $f = self::$setCacheItemTags;

        foreach ($items as $key => $item) {
            if (!$tagKeys) {
                yield $key => $f($item, static::TAGS_PREFIX.$key, $itemTags);
                continue;
            }
            if (!isset($tagKeys[$key])) {
                $bufferedItems[$key] = $item;
                continue;
            }

            unset($tagKeys[$key]);

            if ($item->isHit()) {
                $itemTags[$key] = $item->get() ?: [];
            }

            if (!$tagKeys) {
                $tagVersions = $this->getTagVersions($itemTags);

                foreach ($itemTags as $key => $tags) {
                    foreach ($tags as $tag => $version) {
                        if ($tagVersions[$tag] !== $version) {
                            unset($itemTags[$key]);
                            continue 2;
                        }
                    }
                }
                $tagVersions = $tagKeys = null;

                foreach ($bufferedItems as $key => $item) {
                    yield $key => $f($item, static::TAGS_PREFIX.$key, $itemTags);
                }
                $bufferedItems = null;
            }
        }
    }

    private function getTagVersions(array $tagsByKey)
    {
        $tagVersions = [];
        $fetchTagVersions = false;

        foreach ($tagsByKey as $tags) {
            $tagVersions += $tags;

            foreach ($tags as $tag => $version) {
                if ($tagVersions[$tag] !== $version) {
                    unset($this->knownTagVersions[$tag]);
                }
            }
        }

        if (!$tagVersions) {
            return [];
        }

        $now = microtime(true);
        $tags = [];
        foreach ($tagVersions as $tag => $version) {
            $tags[$tag.static::TAGS_PREFIX] = $tag;
            if ($fetchTagVersions || ($this->knownTagVersions[$tag][1] ?? null) !== $version || $now - $this->knownTagVersions[$tag][0] >= $this->knownTagVersionsTtl) {
                // reuse previously fetched tag versions up to the ttl
                $fetchTagVersions = true;
            }
        }

        if (!$fetchTagVersions) {
            return $tagVersions;
        }

        $newTags = [];
        $newVersion = null;
        foreach ($this->tags->getItems(array_keys($tags)) as $tag => $version) {
            if (!$version->isHit()) {
                $newTags[$tag] = $version->set($newVersion ?? $newVersion = random_int(\PHP_INT_MIN, \PHP_INT_MAX));
            }
            $tagVersions[$tag = $tags[$tag]] = $version->get();
            $this->knownTagVersions[$tag] = [$now, $tagVersions[$tag]];
        }

        if ($newTags) {
            (self::$saveTags)($this->tags, $newTags);
        }

        return $tagVersions;
    }
}
