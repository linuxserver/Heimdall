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

use League\Uri\Exceptions\SyntaxError;

use function count;
use function ord;
use function preg_match;
use function preg_replace;
use function sprintf;
use function strlen;

final class PathToRegexp
{
    /**
     * @return list<Token>
     */
    public static function lex(string $input, bool $lenient = false): array
    {
        $tokens = [];
        $length = strlen($input);
        $i = 0;

        $errorOrInvalid = static function (string $message) use (&$tokens, &$i, $input, $lenient): void {
            $lenient || throw new SyntaxError($message);
            $tokens[] = new Token(TokenType::InvalidChar, $input[$i], $i++);
        };

        while ($i < $length) {
            $char = $input[$i];

            if ('*' === $char) {
                $tokens[] = new Token(TokenType::Asterisk, $input[$i], $i++);
                continue;
            }

            if ('+' === $char || '?' === $char) {
                $tokens[] = new Token(TokenType::OtherModifier, $input[$i], $i++);
                continue;
            }

            if ('\\' === $char) {
                $tokens[] = new Token(TokenType::EscapedChar, $input[$i + 1] ?? '', $i++);
                ++$i;
                continue;
            }

            if ('{' === $char) {
                $tokens[] = new Token(TokenType::Open, $input[$i], $i++);
                continue;
            }

            if ('}' === $char) {
                $tokens[] = new Token(TokenType::Close, $input[$i], $i++);
                continue;
            }

            if (':' === $char) {
                $name = '';
                $j = $i + 1;

                while ($j < $length) {
                    $code = $input[$j];

                    if (
                        ($j === $i + 1 && self::isIdentifierStart($code))
                        || ($j !== $i + 1 && self::isIdentifierPart($code))
                    ) {
                        $name .= $code;
                        ++$j;
                        continue;
                    }

                    break;
                }

                if ('' === $name) {
                    $errorOrInvalid("Missing parameter name at {$i}");
                    continue;
                }

                $tokens[] = new Token(TokenType::Name, $name, $i);
                $i = $j;

                continue;
            }

            if ('(' === $char) {
                $count = 1;
                $pattern = '';
                $j = $i + 1;
                $error = false;

                if ($j < $length && '?' === $input[$j]) {
                    $errorOrInvalid('Pattern cannot start with "?" at '.$j);
                    continue;
                }

                while ($j < $length) {
                    $char = $input[$j];

                    if (!self::isAscii($char)) {
                        $errorOrInvalid("Invalid character '{$char}' at {$j}.");
                        $error = true;
                        break;
                    }

                    if ('\\' === $char) {
                        $pattern .= $input[$j++];
                        if ($j < $length) {
                            $pattern .= $input[$j++];
                        }
                        continue;
                    }

                    if (')' === $char) {
                        --$count;

                        if (0 === $count) {
                            ++$j;
                            break;
                        }
                    } elseif ('(' === $char) {
                        ++$count;

                        if ($j + 1 < $length && '?' !== $input[$j + 1]) {
                            $errorOrInvalid("Capturing groups are not allowed at {$j}");
                            $error = true;
                            break;
                        }
                    }

                    $pattern .= $input[$j++];
                }

                if ($error) {
                    continue;
                }

                if ($count > 0) {
                    $errorOrInvalid("Unbalanced pattern at {$i}");
                    continue;
                }

                if ('' === $pattern) {
                    $errorOrInvalid("Missing pattern at {$i}");
                    continue;
                }

                $tokens[] = new Token(TokenType::Regex, $pattern, $i);
                $i = $j;

                continue;
            }

            $tokens[] = new Token(TokenType::Char, $input[$i], $i++);
        }

        $tokens[] = new Token(TokenType::End, '', $i);

        return $tokens;
    }
    /**
     * @return list<Part>
     */
    public static function parse(
        string $input,
        string $delimiter = '/#?',
        string $prefixes = './',
    ): array {
        $tokens = self::lex($input);

        $segmentWildcardRegex = '[^'.self::escapeString($delimiter).']+?';

        $result = [];
        $key = 0;
        $index = 0;
        $pendingFixedValue = '';
        $names = [];

        $tryConsume = static function (TokenType $type) use (&$tokens, &$index): ?string {
            if ($index < count($tokens) && $tokens[$index]->type === $type) {
                return $tokens[$index++]->value;
            }

            return null;
        };

        $tryConsumeModifier = static function () use (&$tryConsume): ?string {
            return $tryConsume(TokenType::OtherModifier)
                ?? $tryConsume(TokenType::Asterisk);
        };

        $mustConsume = static function (TokenType $type) use (
            &$tokens,
            &$index,
            &$tryConsume,
        ): string {
            $value = $tryConsume($type);

            if (null !== $value) {
                return $value;
            }

            $token = $tokens[$index];

            throw new SyntaxError(
                sprintf(
                    'Unexpected %s at %d, expected %s',
                    $token->type->name,
                    $token->index,
                    $type->name,
                ),
            );
        };

        $consumeText = static function () use (&$tryConsume): string {
            $result = '';

            while (null !== ($value = $tryConsume(TokenType::Char))
                || null !== ($value = $tryConsume(TokenType::EscapedChar))
            ) {
                $result .= $value;
            }

            return $result;
        };

        $maybeAddPartFromPendingFixedValue = static function () use (
            &$pendingFixedValue,
            &$result,
        ): void {
            /* @phpstan-ignore-next-line */
            if ('' !== $pendingFixedValue) {
                $result[] = new Part(PartType::Fixed, '', '', $pendingFixedValue, '', Modifier::None);
                $pendingFixedValue = '';
            }
        };

        $addPart = static function (
            string $prefix,
            ?string $nameToken,
            ?string $regexOrWildcardToken,
            string $suffix,
            ?string $modifierToken,
        ) use (
            &$result,
            &$key,
            &$names,
            &$pendingFixedValue,
            &$maybeAddPartFromPendingFixedValue,
            $segmentWildcardRegex,
        ): void {
            $modifier = match ($modifierToken) {
                '?' => Modifier::Optional,
                '*' => Modifier::ZeroOrMore,
                '+' => Modifier::OneOrMore,
                default => Modifier::None,
            };

            if (null === $nameToken && null === $regexOrWildcardToken && Modifier::None === $modifier) {
                $pendingFixedValue .= $prefix;

                return;
            }

            $maybeAddPartFromPendingFixedValue();

            if (null === $nameToken && null === $regexOrWildcardToken) {
                if ('' === $prefix) {
                    return;
                }

                $result[] = new Part(PartType::Fixed, '', '', $prefix, '', $modifier);

                return;
            }

            $regexValue = match (true) {
                null === $regexOrWildcardToken => $segmentWildcardRegex,
                '*' === $regexOrWildcardToken => '.*',
                default => $regexOrWildcardToken,
            };

            $type = match ($regexValue) {
                $segmentWildcardRegex => PartType::SegmentWildcard,
                '.*' => PartType::FullWildcard,
                default => PartType::Regex,
            };

            if (PartType::Regex !== $type) {
                $regexValue = '';
            }

            $name = $nameToken;

            if (null === $name && null !== $regexOrWildcardToken) {
                $name = $key++;
            }

            !isset($names[$name]) || throw new SyntaxError(sprintf("Duplicate name '%s'.", $name));

            $names[$name] = true;
            $result[] = new Part($type, (string) $name, $prefix, $regexValue, $suffix, $modifier);
        };

        while ($index < count($tokens)) {
            $charToken = $tryConsume(TokenType::Char);
            $nameToken = $tryConsume(TokenType::Name);
            $regexOrWildcardToken = $tryConsume(TokenType::Regex);

            if (null === $nameToken && null === $regexOrWildcardToken) {
                $regexOrWildcardToken = $tryConsume(TokenType::Asterisk);
            }

            if (null !== $nameToken || null !== $regexOrWildcardToken) {
                $prefix = $charToken ?? '';
                if (! str_contains($prefixes, $prefix)) {
                    $pendingFixedValue .= $prefix;
                    $prefix = '';
                }

                $maybeAddPartFromPendingFixedValue();
                $modifierToken = $tryConsumeModifier();
                $addPart($prefix, $nameToken, $regexOrWildcardToken, '', $modifierToken);

                continue;
            }

            $value = $charToken ?? $tryConsume(TokenType::EscapedChar);
            if (null !== $value) {
                $pendingFixedValue .= $value;

                continue;
            }

            $openToken = $tryConsume(TokenType::Open);

            if (null !== $openToken) {
                $prefix = $consumeText();
                $nameToken = $tryConsume(TokenType::Name);
                $regexOrWildcardToken = $tryConsume(TokenType::Regex);
                if (null === $nameToken && null === $regexOrWildcardToken) {
                    $regexOrWildcardToken = $tryConsume(TokenType::Asterisk);
                }

                $suffix = $consumeText();
                $mustConsume(TokenType::Close);
                $modifierToken = $tryConsumeModifier();
                $addPart($prefix, $nameToken, $regexOrWildcardToken, $suffix, $modifierToken);
                continue;
            }

            $maybeAddPartFromPendingFixedValue();
            $mustConsume(TokenType::End);
        }

        return $result;
    }

    /**
     * @param list<Part> $parts
     * @param list<string|int>|null $names
     */
    public static function partsToRegexp(
        array $parts,
        ?array &$names = null,
        array $options = [],
    ): string {
        $delimiter = $options['delimiter'] ?? '/#?';
        $strict = $options['strict'] ?? false;
        $end = $options['end'] ?? true;
        $start = $options['start'] ?? true;

        $endsWith = '';

        $result = $start ? '^' : '';

        foreach ($parts as $part) {
            if (PartType::Fixed === $part->type) {
                if (Modifier::None === $part->modifier) {
                    $result .= self::escapeString($part->value);
                } else {
                    $result .= '(?:'
                        .self::escapeString($part->value)
                        .')'
                        .$part->modifier->value;
                }

                continue;
            }

            if (null !== $names) {
                $names[] = $part->name;
            }

            $segmentWildcardRegex = '[^'
                .self::escapeString($delimiter)
                .']+?';

            $regexValue = match ($part->type) {
                PartType::SegmentWildcard => $segmentWildcardRegex,
                PartType::FullWildcard => '.*',
                PartType::Regex => $part->value,
            };

            if ('' === $part->prefix && '' === $part->suffix) {
                if (
                    Modifier::None === $part->modifier
                    || Modifier::Optional === $part->modifier
                ) {
                    $result .= '('
                        .$regexValue
                        .')'
                        .$part->modifier->value;
                } else {
                    $result .= '((?:'
                        .$regexValue
                        .')'
                        .$part->modifier->value
                        .')';
                }

                continue;
            }

            if (
                Modifier::None === $part->modifier
                || Modifier::Optional === $part->modifier
            ) {
                $result .= '(?:'
                    .self::escapeString($part->prefix)
                    .'('
                    .$regexValue
                    .')'
                    .self::escapeString($part->suffix)
                    .')'
                    .$part->modifier->value;

                continue;
            }

            $result .= '(?:'
                .self::escapeString($part->prefix)
                .'((?:'
                .$regexValue
                .')(?:'
                .self::escapeString($part->suffix)
                .self::escapeString($part->prefix)
                .'(?:'
                .$regexValue
                .'))*)'
                .self::escapeString($part->suffix)
                .')';

            if (Modifier::ZeroOrMore === $part->modifier) {
                $result .= '?';
            }
        }

        $endsWithRegex = '['.self::escapeString($endsWith).']|$';
        $delimiterRegex = '['.self::escapeString($delimiter).']';

        if ($end) {
            if (! $strict) {
                $result .= $delimiterRegex.'?';
            }

            /* @phpstan-ignore-next-line  */
            return '' === $endsWith ? $result.'$' : $result.'(?='.$endsWithRegex.')';
        }

        if (! $strict) {
            $result .= '(?:'
                .$delimiterRegex
                .'(?='
                .$endsWithRegex
                .'))?';
        }

        $isEndDelimited = false;

        if ([] !== $parts) {
            $lastPart = $parts[array_key_last($parts)];

            if (
                PartType::Fixed === $lastPart->type
                && Modifier::None === $lastPart->modifier
            ) {
                $isEndDelimited = str_contains(
                    $delimiter,
                    $lastPart->value,
                );
            }
        }

        if (! $isEndDelimited) {
            $result .= '(?='
                .$delimiterRegex
                .'|'
                .$endsWithRegex
                .')';
        }

        return $result;
    }

    /**
     * @param list<array-key>|null $names
     */
    public static function stringToRegexp(
        string $path,
        ?array &$names = null,
        array $options = [],
    ): string {
        return self::partsToRegexp(self::parse($path), $names, $options);
    }

    private static function isAscii(string $char): bool
    {
        return ord($char) <= 0x7f;
    }

    private static function isIdentifierStart(string $char): bool
    {
        return 1 === preg_match('/^[\p{L}_$]$/u', $char);
    }

    private static function isIdentifierPart(string $char): bool
    {
        return 1 === preg_match('/^[\p{L}\p{N}_$]$/u', $char);
    }

    private static function escapeString(string $value): string
    {
        return (string) preg_replace('/([.+*?^${}()[\]|\/\\\\])/', '\\\\$1', $value);
    }
}
