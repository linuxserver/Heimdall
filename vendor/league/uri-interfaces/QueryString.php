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

use League\Uri\Exceptions\SyntaxError;
use League\Uri\KeyValuePair\Converter;
use Stringable;

use function array_key_exists;
use function array_keys;
use function is_array;
use function rawurldecode;
use function strpos;
use function substr;

use const PHP_QUERY_RFC3986;

/**
 * A class to parse the URI query string.
 *
 * @see https://tools.ietf.org/html/rfc3986#section-3.4
 */
final class QueryString
{
    private const PAIR_VALUE_DECODED = 1;
    private const PAIR_VALUE_PRESERVED = 2;

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Build a query string from a list of pairs.
     *
     * @see QueryString::buildFromPairs()
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-2.2
     *
     * @param iterable<array{0:string, 1:string|float|int|bool|null}> $pairs
     * @param non-empty-string $separator
     *
     * @throws SyntaxError If the encoding type is invalid
     * @throws SyntaxError If a pair is invalid
     */
    public static function build(iterable $pairs, string $separator = '&', int $encType = PHP_QUERY_RFC3986): ?string
    {
        return self::buildFromPairs($pairs, Converter::fromEncodingType($encType)->withSeparator($separator));
    }

    /**
     * Build a query string from a list of pairs.
     *
     * The method expects the return value from Query::parse to build
     * a valid query string. This method differs from PHP http_build_query as
     * it does not modify parameters keys.
     *
     *  If a reserved character is found in a URI component and
     *  no delimiting role is known for that character, then it must be
     *  interpreted as representing the data octet corresponding to that
     *  character's encoding in US-ASCII.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-2.2
     *
     * @param iterable<array{0:string, 1:string|float|int|bool|null}> $pairs
     *
     * @throws SyntaxError If the encoding type is invalid
     * @throws SyntaxError If a pair is invalid
     */
    public static function buildFromPairs(iterable $pairs, ?Converter $converter = null): ?string
    {
        $keyValuePairs = [];
        foreach ($pairs as $pair) {
            if (!is_array($pair) || [0, 1] !== array_keys($pair)) {
                throw new SyntaxError('A pair must be a sequential array starting at `0` and containing two elements.');
            }

            $keyValuePairs[] = [(string) Encoder::encodeQueryKeyValue($pair[0]), match(null) {
                $pair[1] => null,
                default => Encoder::encodeQueryKeyValue($pair[1]),
            }];
        }

        return ($converter ?? Converter::fromRFC3986())->toValue($keyValuePairs);
    }

    /**
     * Parses the query string like parse_str without mangling the results.
     *
     * @see QueryString::extractFromValue()
     * @see http://php.net/parse_str
     * @see https://wiki.php.net/rfc/on_demand_name_mangling
     *
     * @param non-empty-string $separator
     *
     * @throws SyntaxError
     */
    public static function extract(Stringable|string|bool|null $query, string $separator = '&', int $encType = PHP_QUERY_RFC3986): array
    {
        return self::extractFromValue($query, Converter::fromEncodingType($encType)->withSeparator($separator));
    }

    /**
     * Parses the query string like parse_str without mangling the results.
     *
     * The result is similar as PHP parse_str when used with its
     * second argument with the difference that variable names are
     * not mangled.
     *
     * @see http://php.net/parse_str
     * @see https://wiki.php.net/rfc/on_demand_name_mangling
     *
     * @throws SyntaxError
     */
    public static function extractFromValue(Stringable|string|bool|null $query, ?Converter $converter = null): array
    {
        return self::convert(self::decodePairs(
            ($converter ?? Converter::fromRFC3986())->toPairs($query),
            self::PAIR_VALUE_PRESERVED
        ));
    }

    /**
     * Parses a query string into a collection of key/value pairs.
     *
     * @param non-empty-string $separator
     *
     * @throws SyntaxError
     *
     * @return array<int, array{0:string, 1:string|null}>
     */
    public static function parse(Stringable|string|bool|null $query, string $separator = '&', int $encType = PHP_QUERY_RFC3986): array
    {
        return self::parseFromValue($query, Converter::fromEncodingType($encType)->withSeparator($separator));
    }

    /**
     * Parses a query string into a collection of key/value pairs.
     *
     * @throws SyntaxError
     *
     * @return array<int, array{0:string, 1:string|null}>
     */
    public static function parseFromValue(Stringable|string|bool|null $query, ?Converter $converter = null): array
    {
        return self::decodePairs(
            ($converter ?? Converter::fromRFC3986())->toPairs($query),
            self::PAIR_VALUE_DECODED
        );
    }

    /**
     * @param array<non-empty-list<string|null>> $pairs
     *
     * @return array<int, array{0:string, 1:string|null}>
     */
    private static function decodePairs(array $pairs, int $pairValueState): array
    {
        $decodePair = static function (array $pair, int $pairValueState): array {
            [$key, $value] = $pair;

            return match ($pairValueState) {
                self::PAIR_VALUE_PRESERVED => [(string) Encoder::decodeAll($key), $value],
                default => [(string) Encoder::decodeAll($key), Encoder::decodeAll($value)],
            };
        };

        return array_reduce(
            $pairs,
            fn (array $carry, array $pair) => [...$carry, $decodePair($pair, $pairValueState)],
            []
        );
    }

    /**
     * Converts a collection of key/value pairs and returns
     * the store PHP variables as elements of an array.
     */
    public static function convert(iterable $pairs): array
    {
        $returnedValue = [];
        foreach ($pairs as $pair) {
            $returnedValue = self::extractPhpVariable($returnedValue, $pair);
        }

        return $returnedValue;
    }

    /**
     * Parses a query pair like parse_str without mangling the results array keys.
     *
     * <ul>
     * <li>empty name are not saved</li>
     * <li>If the value from name is duplicated its corresponding value will be overwritten</li>
     * <li>if no "[" is detected the value is added to the return array with the name as index</li>
     * <li>if no "]" is detected after detecting a "[" the value is added to the return array with the name as index</li>
     * <li>if there's a mismatch in bracket usage the remaining part is dropped</li>
     * <li>“.” and “ ” are not converted to “_”</li>
     * <li>If there is no “]”, then the first “[” is not converted to becomes an “_”</li>
     * <li>no whitespace trimming is done on the key value</li>
     * </ul>
     *
     * @see https://php.net/parse_str
     * @see https://wiki.php.net/rfc/on_demand_name_mangling
     * @see https://github.com/php/php-src/blob/master/ext/standard/tests/strings/parse_str_basic1.phpt
     * @see https://github.com/php/php-src/blob/master/ext/standard/tests/strings/parse_str_basic2.phpt
     * @see https://github.com/php/php-src/blob/master/ext/standard/tests/strings/parse_str_basic3.phpt
     * @see https://github.com/php/php-src/blob/master/ext/standard/tests/strings/parse_str_basic4.phpt
     *
     * @param array $data the submitted array
     * @param array|string $name the pair key
     * @param string $value the pair value
     */
    private static function extractPhpVariable(array $data, array|string $name, string $value = ''): array
    {
        if (is_array($name)) {
            [$name, $value] = $name;
            $value = rawurldecode((string) $value);
        }

        if ('' === $name) {
            return $data;
        }

        $leftBracketPosition = strpos($name, '[');
        if (false === $leftBracketPosition) {
            $data[$name] = $value;

            return $data;
        }

        $rightBracketPosition = strpos($name, ']', $leftBracketPosition);
        if (false === $rightBracketPosition) {
            $data[$name] = $value;

            return $data;
        }

        $key = substr($name, 0, $leftBracketPosition);
        if ('' === $key) {
            $key = '0';
        }

        if (!array_key_exists($key, $data) || !is_array($data[$key])) {
            $data[$key] = [];
        }

        $remaining = substr($name, $rightBracketPosition + 1);
        if (!str_starts_with($remaining, '[') || !str_contains($remaining, ']')) {
            $remaining = '';
        }

        $name = substr($name, $leftBracketPosition + 1, $rightBracketPosition - $leftBracketPosition - 1).$remaining;
        if ('' === $name) {
            $data[$key][] = $value;

            return $data;
        }

        $data[$key] = self::extractPhpVariable($data[$key], $name, $value);

        return $data;
    }
}
