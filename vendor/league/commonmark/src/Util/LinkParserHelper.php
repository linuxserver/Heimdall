<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

use League\CommonMark\Parser\Cursor;

/**
 * @psalm-immutable
 */
final class LinkParserHelper
{
    /**
     * Attempt to parse link destination
     *
     * @return string|null The string, or null if no match
     */
    public static function parseLinkDestination(Cursor $cursor): ?string
    {
        if ($cursor->getCurrentCharacter() === '<') {
            return self::parseDestinationBraces($cursor);
        }

        $destination = self::manuallyParseLinkDestination($cursor);
        if ($destination === null) {
            return null;
        }

        return UrlEncoder::unescapeAndEncode(
            RegexHelper::unescape($destination)
        );
    }

    public static function parseLinkLabel(Cursor $cursor): int
    {
        $match = $cursor->matchInPlace('/\G\[(?:[^\\\\\[\]]|\\\\.){0,1000}\]/');
        if ($match === null) {
            return 0;
        }

        $length = \mb_strlen($match, 'UTF-8');

        if ($length > 1001) {
            return 0;
        }

        return $length;
    }

    public static function parsePartialLinkLabel(Cursor $cursor): ?string
    {
        return $cursor->matchInPlace('/\G(?:[^\\\\\[\]]++|\\\\.?)*+/');
    }

    /**
     * Attempt to parse link title (sans quotes)
     *
     * @return string|null The string, or null if no match
     */
    public static function parseLinkTitle(Cursor $cursor): ?string
    {
        if ($title = $cursor->matchInPlace('/\G' . RegexHelper::PARTIAL_LINK_TITLE_UNANCHORED . '/')) {
            // Chop off quotes from title and unescape
            return RegexHelper::unescape(\substr($title, 1, -1));
        }

        return null;
    }

    public static function parsePartialLinkTitle(Cursor $cursor, string $endDelimiter): ?string
    {
        $endDelimiter = \preg_quote($endDelimiter, '/');
        $regex        = \sprintf('/(%s|[^%s\x00])*(?:%s)?/', RegexHelper::PARTIAL_ESCAPED_CHAR, $endDelimiter, $endDelimiter);
        if (($partialTitle = $cursor->matchInPlace($regex)) === null) {
            return null;
        }

        return RegexHelper::unescape($partialTitle);
    }

    private static function manuallyParseLinkDestination(Cursor $cursor): ?string
    {
        // The destination always ends at the first whitespace or unbalanced ")", so scan the line
        // in place from the cursor rather than materializing the remainder: the cost of finding it
        // should follow the length of the destination, not the length of everything left in the
        // block. A partially-consumed tab needs no special handling - getRemainder() would expand
        // it into leading spaces and the scan would stop on the first one, exactly as it stops on
        // the tab itself here.
        $line       = $cursor->getLine();
        $start      = $cursor->getBytePosition();
        $openParens = 0;
        $len        = \strlen($line) - $start;
        for ($i = 0; $i < $len; $i++) {
            $c = $line[$start + $i];
            if ($c === '\\' && $i + 1 < $len && RegexHelper::isEscapable($line[$start + $i + 1])) {
                $i++;
            } elseif ($c === '(') {
                $openParens++;
                // Limit to 32 nested parens for pathological cases
                if ($openParens > 32) {
                    return null;
                }
            } elseif ($c === ')') {
                if ($openParens < 1) {
                    break;
                }

                $openParens--;
            } elseif (\ord($c) <= 32 && RegexHelper::isWhitespace($c)) {
                break;
            }
        }

        if ($openParens !== 0) {
            return null;
        }

        if ($i === 0 && (! isset($c) || $c !== ')')) {
            return null;
        }

        $destination = \substr($line, $start, $i);
        $cursor->advanceBy(\mb_strlen($destination, 'UTF-8'));

        return $destination;
    }

    /** @var \WeakReference<Cursor>|null */
    private static ?\WeakReference $lastCursor       = null;
    private static bool $lastCursorLacksClosingBrace = false;

    private static function parseDestinationBraces(Cursor $cursor): ?string
    {
        // Optimization: If we've previously parsed this cursor and returned `null`, we know
        // that no closing brace exists, so we can skip the regex entirely. This helps avoid
        // certain pathological cases where the regex engine can take a very long time to
        // determine that no match exists.
        if (self::$lastCursor !== null && self::$lastCursor->get() === $cursor) {
            if (self::$lastCursorLacksClosingBrace) {
                return null;
            }
        } else {
            self::$lastCursor = \WeakReference::create($cursor);
        }

        if ($res = $cursor->matchInPlace('/\G' . RegexHelper::PARTIAL_LINK_DESTINATION_BRACES . '/')) {
            self::$lastCursorLacksClosingBrace = false;

            // Chop off surrounding <..>:
            return UrlEncoder::unescapeAndEncode(
                RegexHelper::unescape(\substr($res, 1, -1))
            );
        }

        self::$lastCursorLacksClosingBrace = true;

        return null;
    }
}
