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
    private const REGEX = '/^\s*([.#][_a-z0-9-]+|' . RegexHelper::PARTIAL_ATTRIBUTENAME . RegexHelper::PARTIAL_ATTRIBUTEVALUESPEC . ')(?<!})\s*/i';

    /**
     * @return array<string, mixed>
     */
    public static function parseAttributes(Cursor $cursor): array
    {
        $state = $cursor->saveState();
        $cursor->advanceToNextNonSpaceOrNewline();
        if ($cursor->getCurrentCharacter() !== '{') {
            $cursor->restoreState($state);

            return [];
        }

        $cursor->advanceBy(1);
        if ($cursor->getCurrentCharacter() === ':') {
            $cursor->advanceBy(1);
        }

        $attributes = [];
        while ($attribute = \trim((string) $cursor->match(self::REGEX))) {
            if ($attribute[0] === '#') {
                $attributes['id'] = \substr($attribute, 1);

                continue;
            }

            if ($attribute[0] === '.') {
                $attributes['class'][] = \substr($attribute, 1);

                continue;
            }

            [$name, $value] = \explode('=', $attribute, 2);

            $first = $value[0];
            $last  = \substr($value, -1);
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'") && \strlen($value) > 1) {
                $value = \substr($value, 1, -1);
            }

            if (\strtolower(\trim($name)) === 'class') {
                foreach (\array_filter(\explode(' ', \trim($value))) as $class) {
                    $attributes['class'][] = $class;
                }
            } else {
                $attributes[\trim($name)] = \trim($value);
            }
        }

        if ($cursor->match('/}/') === null) {
            $cursor->restoreState($state);

            return [];
        }

        if ($attributes === []) {
            $cursor->restoreState($state);

            return [];
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
                if (\is_string($arg['class'])) {
                    $arg['class'] = \array_filter(\explode(' ', \trim($arg['class'])));
                }

                foreach ($arg['class'] as $class) {
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
}
