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

namespace League\Uri\UriTemplate;

use function preg_replace_callback;
use function rawurlencode;
use function strlen;

/**
 * @internal The class exposes the internal representation of an Literal string
 * @link https://www.rfc-editor.org/rfc/rfc6570#section-3.1
 */
final class Literal
{
    private const REGEXP_CHARACTERS_TO_ENCODE = '/[^A-Za-z\d\-._~:\/?#\[\]@!$&\'()*+,;=%]+|%(?![A-Fa-f\d]{2})/';

    public readonly string $encoded;
    public readonly int $length;

    public function __construct(public readonly string $raw)
    {
        $this->encoded = self::encode($raw);
        $this->length = strlen($this->encoded);
    }

    private static function encode(string $raw): string
    {
        return (string) preg_replace_callback(
            self::REGEXP_CHARACTERS_TO_ENCODE,
            static fn (array $matches): string => rawurlencode($matches[0]),
            $raw,
        );
    }

    public function isPrefixOf(string $value): bool
    {
        return str_starts_with($value, $this->encoded);
    }
}
