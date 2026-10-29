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

enum PartType
{
    case FullWildcard;
    case SegmentWildcard;
    case Regex;
    case Fixed;

    public function hasVariable(): bool
    {
        return self::Fixed !== $this;
    }

    public function hasRegexpGroup(): bool
    {
        return self::Regex === $this;
    }
}
