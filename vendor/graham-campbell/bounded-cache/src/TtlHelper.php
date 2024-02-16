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

use DateInterval;
use DateTimeImmutable;

/**
 * This is TTL helper.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class TtlHelper
{
    /**
     * Computes the correct TTL to use.
     */
    public static function computeTtl(int $min, int $max, null|int|\DateInterval $ttl = null)
    {
        if ($ttl instanceof DateInterval) {
            $ttl = self::dateIntervalToSeconds($ttl);
        }

        return max($min, min($ttl ?: $min, $max));
    }

    /**
     * Convert a date interval to seconds.
     */
    private static function dateIntervalToSeconds(DateInterval $ttl): int
    {
        $reference = (new DateTimeImmutable())->setTimestamp(0);

        return $reference->add($ttl)->getTimestamp();
    }
}
