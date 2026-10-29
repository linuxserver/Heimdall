<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) 2015 Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Attributes\Util;

use League\CommonMark\Node\Node;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Util\RegexHelper;

/**
 * @internal
 */
final class AttributesHelper
{
    private const SINGLE_ATTRIBUTE = '\s*([.]-?[_a-z][^\s.}]*|[#][^\s}]+|' . RegexHelper::PARTIAL_ATTRIBUTENAME . RegexHelper::PARTIAL_ATTRIBUTEVALUESPEC . ')\s*';
    private const ATTRIBUTE_LIST   = '/\G{:?(' . self::SINGLE_ATTRIBUTE . ')+}/i';

    /**
     * PCRE's `\s` matches the form feed that PHP's default trim charlist omits, so the
     * separators SINGLE_ATTRIBUTE accepts must be trimmed with this list instead - otherwise
     * that byte survives inside an attribute name, where a browser reads it as a separator.
     */
    private const WHITESPACE = " \t\n\r\0\x0B\x0C";

    /**
     * @return array<string, mixed>
     */
    public static function parseAttributes(Cursor $cursor): array
    {
        $state = $cursor->saveState();
        $cursor->advanceToNextNonSpaceOrNewline();

        // Quick check to see if we might have attributes
        if ($cursor->getCharacter() !== '{') {
            $cursor->restoreState($state);

            return [];
        }

        // Attempt to match the entire attribute list expression
        // While this is less performant than checking for '{' now and '}' later, it simplifies
        // matching individual attributes since they won't need to look ahead for the closing '}'
        // while dealing with the fact that attributes can technically contain curly braces.
        // So we'll just match the start and end braces up front.
        $attributeExpression = $cursor->matchInPlace(self::ATTRIBUTE_LIST);
        if ($attributeExpression === null) {
            $cursor->restoreState($state);

            return [];
        }

        // Trim the leading '{' or '{:' and the trailing '}'
        $attributeExpression = \ltrim(\substr($attributeExpression, 1, -1), ':');
        $attributeCursor     = new Cursor($attributeExpression);

        /** @var array<string, mixed> $attributes */
        $attributes = [];
        while ($attribute = \trim((string) $attributeCursor->matchInPlace('/\G' . self::SINGLE_ATTRIBUTE . '/i'), self::WHITESPACE)) {
            if ($attribute[0] === '#') {
                $attributes['id'] = \substr($attribute, 1);

                continue;
            }

            if ($attribute[0] === '.') {
                $attributes['class'][] = \substr($attribute, 1);

                continue;
            }

            /** @psalm-suppress PossiblyUndefinedArrayOffset */
            [$name, $value] = \explode('=', $attribute, 2);

            if ($value === 'true') {
                $attributes[$name] = true;
                continue;
            }

            $first = $value[0];
            $last  = \substr($value, -1);
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'") && \strlen($value) > 1) {
                $value = \substr($value, 1, -1);
            }

            if (\strtolower(\trim($name, self::WHITESPACE)) === 'class') {
                foreach (\array_filter(\explode(' ', \trim($value, self::WHITESPACE))) as $class) {
                    $attributes['class'][] = $class;
                }
            } else {
                $attributes[\trim($name, self::WHITESPACE)] = \trim($value, self::WHITESPACE);
            }
        }

        if (isset($attributes['class'])) {
            $attributes['class'] = \implode(' ', (array) $attributes['class']);
        }

        return $attributes;
    }

    /**
     * @param Node|array<string, mixed> $attributes1
     * @param Node|array<string, mixed> $attributes2
     *
     * @return array<string, mixed>
     */
    public static function mergeAttributes($attributes1, $attributes2): array
    {
        $attributes = [];
        foreach ([$attributes1, $attributes2] as $arg) {
            if ($arg instanceof Node) {
                $arg = $arg->data->get('attributes');
            }

            /** @var array<string, mixed> $arg */
            $arg = (array) $arg;
            if (isset($arg['class'])) {
                foreach (self::classList($arg['class']) as $class) {
                    $attributes['class'][] = $class;
                }

                unset($arg['class']);
            }

            $attributes = \array_merge($attributes, $arg);
        }

        if (isset($attributes['class'])) {
            $attributes['class'] = \implode(' ', $attributes['class']);
        }

        return $attributes;
    }

    /**
     * Split a `class` attribute value into the individual classes it contributes to a merge
     *
     * @param mixed $class
     *
     * @return list<string>
     */
    public static function classList($class): array
    {
        if (\is_string($class)) {
            return \array_values(\array_filter(\explode(' ', \trim($class))));
        }

        return \array_values((array) $class);
    }

    /**
     * @param array<string, mixed> $attributes
     * @param list<string>         $allowList
     *
     * @return array<string, mixed>
     */
    public static function filterAttributes(array $attributes, array $allowList, bool $allowUnsafeLinks): array
    {
        $allowList = \array_fill_keys($allowList, true);

        foreach ($attributes as $name => $value) {
            // The checks below compare against literal names, and the renderer emits names
            // without escaping them, so anything that isn't a well-formed attribute name
            // would slip past both
            if (\preg_match('/^' . RegexHelper::PARTIAL_ATTRIBUTENAME . '$/i', (string) $name) !== 1) {
                unset($attributes[$name]);
                continue;
            }

            $attrNameLower = \strtolower((string) $name);

            // Remove any unsafe links
            if (! $allowUnsafeLinks && ($attrNameLower === 'href' || $attrNameLower === 'src') && \is_string($value) && RegexHelper::isLinkPotentiallyUnsafe($value)) {
                unset($attributes[$name]);
                continue;
            }

            // No allowlist?
            if ($allowList === []) {
                // Just remove JS event handlers
                if (\str_starts_with($attrNameLower, 'on')) {
                    unset($attributes[$name]);
                }

                continue;
            }

            // Remove any attributes not in that allowlist (case-sensitive)
            if (! isset($allowList[$name])) {
                unset($attributes[$name]);
            }
        }

        return $attributes;
    }
}
