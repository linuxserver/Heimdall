<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\UrlPattern;

final class Part
{
    public function __construct(
        public readonly PartType $type,
        public readonly string|int $name,
        public readonly string $prefix,
        public readonly string $value,
        public readonly string $suffix,
        public readonly Modifier $modifier,
    ) {
    }
}
