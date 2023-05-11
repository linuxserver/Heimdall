<?php

declare(strict_types=1);

/*
 * This file is part of Bounded Cache.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\BoundedCache;

use Psr\SimpleCache\CacheInterface;

/**
 * This is the bounded cache interface.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
interface BoundedCacheInterface extends CacheInterface
{
    /**
     * Get the minimum cache lifetime.
     *
     * @return int
     */
    public function getMinimumLifetime();

    /**
     * Get the maximum cache lfetime.
     *
     * @return int
     */
    public function getMaximumLifetime();
}
