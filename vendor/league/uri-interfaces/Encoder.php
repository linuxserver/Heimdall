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

namespace League\Uri;

use Closure;
use League\Uri\Contracts\UriComponentInterface;
use League\Uri\Exceptions\SyntaxError;
use SensitiveParameter;
use Stringable;

use function preg_match;
use function preg_replace_callback;
use function rawurldecode;
use function rawurlencode;
use function strtoupper;

final class Encoder
{
    private const REGEXP_CHARS_INVALID = '/[\x00-\x1f\x7f]/';
    private const REGEXP_CHARS_ENCODED = ',%[A-Fa-f0-9]{2},';
    private const REGEXP_CHARS_PREVENTS_DECODING = ',%
     	2[A-F|1-2|4-9]|
        3[0-9|B|D]|
        4[1-9|A-F]|
        5[0-9|A|F]|
        6[1-9|A-F]|
        7[0-9|E]
    ,ix';
    private const REGEXP_PART_SUBDELIM = "\!\$&'\(\)\*\+,;\=%";
    private const REGEXP_PART_UNRESERVED = 'A-Za-z\d_\-.~';
    private const REGEXP_PART_ENCODED = '%(?![A-Fa-f\d]{2})';

    /**
     * Encode User.
     *
     * All generic delimiters MUST be encoded
     */
    public static function encodeUser(Stringable|string|null $component): ?string
    {
        static $pattern = '/[^'.self::REGEXP_PART_UNRESERVED.self::REGEXP_PART_SUBDELIM.']+|'.self::REGEXP_PART_ENCODED.'/';

        return self::encode($component, $pattern);
    }

    /**
     * Encode Password.
     *
     * Generic delimiters ":" MUST NOT be encoded
     */
    public static function encodePassword(#[SensitiveParameter] Stringable|string|null $component): ?string
    {
        static $pattern = '/[^'.self::REGEXP_PART_UNRESERVED.self::REGEXP_PART_SUBDELIM.':]+|'.self::REGEXP_PART_ENCODED.'/';

        return self::encode($component, $pattern);
    }

    /**
     * Encode Path.
     *
     * Generic delimiters ":", "@", and "/" MUST NOT be encoded
     */
    public static function encodePath(Stringable|string|null $component): string
    {
        static $pattern = '/[^'.self::REGEXP_PART_UNRESERVED.self::REGEXP_PART_SUBDELIM.':@\/]+|'.self::REGEXP_PART_ENCODED.'/';

        return (string) self::encode($component, $pattern);
    }

    /**
     * Encode Query or Fragment.
     *
     * Generic delimiters ":", "@", "?", and "/" MUST NOT be encoded
     */
    public static function encodeQueryOrFragment(Stringable|string|null $component): ?string
    {
        static $pattern = '/[^'.self::REGEXP_PART_UNRESERVED.self::REGEXP_PART_SUBDELIM.':@\/?]+|'.self::REGEXP_PART_ENCODED.'/';

        return self::encode($component, $pattern);
    }

    public static function encodeQueryKeyValue(mixed $component): ?string
    {
        static $pattern = '/[^'.self::REGEXP_PART_UNRESERVED.']+|'.self::REGEXP_PART_ENCODED.'/';

        $encodeMatches = static fn (array $matches): string => match (1) {
            preg_match('/[^'.self::REGEXP_PART_UNRESERVED.']/', rawurldecode($matches[0])) => rawurlencode($matches[0]),
            default => $matches[0],
        };

        $component = self::filterComponent($component);

        return match (true) {
            !is_scalar($component) => throw new SyntaxError(sprintf('A pair key/value must be a scalar value `%s` given.', gettype($component))),
            1 === preg_match(self::REGEXP_CHARS_INVALID, $component) => rawurlencode($component),
            1 === preg_match($pattern, $component) => (string) preg_replace_callback($pattern, $encodeMatches(...), $component),
            default => $component,
        };
    }

    /**
     * Decodes the URI component without decoding the unreserved characters which are already encoded.
     */
    public static function decodePartial(Stringable|string|int|null $component): ?string
    {
        $decodeMatches = static fn (array $matches): string => match (1) {
            preg_match(self::REGEXP_CHARS_PREVENTS_DECODING, $matches[0]) => strtoupper($matches[0]),
            default => rawurldecode($matches[0]),
        };

        return self::decode($component, $decodeMatches);
    }

    /**
     * Decodes all the URI component characters.
     */
    public static function decodeAll(Stringable|string|int|null $component): ?string
    {
        $decodeMatches = static fn (array $matches): string => rawurldecode($matches[0]);

        return self::decode($component, $decodeMatches);
    }

    private static function filterComponent(mixed $component): ?string
    {
        return match (true) {
            true === $component => '1',
            false === $component => '0',
            $component instanceof UriComponentInterface => $component->value(),
            $component instanceof Stringable,
            is_scalar($component) => (string) $component,
            null === $component => null,
            default => throw new SyntaxError(sprintf('The component must be a scalar value `%s` given.', gettype($component))),
        };
    }

    private static function encode(Stringable|string|int|bool|null $component, string $pattern): ?string
    {
        $component = self::filterComponent($component);
        $encodeMatches = static fn (array $matches): string => match (1) {
            preg_match('/[^'.self::REGEXP_PART_UNRESERVED.']/', rawurldecode($matches[0])) => rawurlencode($matches[0]),
            default => $matches[0],
        };

        return match (true) {
            null === $component,
            '' === $component => $component,
            default => (string) preg_replace_callback($pattern, $encodeMatches(...), $component),
        };
    }

    /**
     * Decodes all the URI component characters.
     */
    private static function decode(Stringable|string|int|null $component, Closure $decodeMatches): ?string
    {
        $component = self::filterComponent($component);

        return match (true) {
            null === $component => null,
            1 === preg_match(self::REGEXP_CHARS_INVALID, $component) => throw new SyntaxError('Invalid component string: '.$component.'.'),
            1 === preg_match(self::REGEXP_CHARS_ENCODED, $component) => preg_replace_callback(self::REGEXP_CHARS_ENCODED, $decodeMatches(...), $component),
            default => $component,
        };
    }
}
