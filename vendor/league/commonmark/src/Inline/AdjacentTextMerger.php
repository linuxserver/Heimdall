<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Additional emphasis processing code based on commonmark-java (https://github.com/atlassian/commonmark-java)
 *  - (c) Atlassian Pty Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Inline;

use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Node\Node;

/**
 * @internal
 */
final class AdjacentTextMerger
{
    public static function mergeChildNodes(Node $node): void
    {
        // No children or just one child node, no need for merging
        if ($node->firstChild() === $node->lastChild() || $node->firstChild() === null || $node->lastChild() === null) {
            return;
        }

        self::mergeTextNodesInclusive($node->firstChild(), $node->lastChild());
    }

    public static function mergeTextNodesBetweenExclusive(Node $fromNode, Node $toNode): void
    {
        // No nodes between them
        if ($fromNode === $toNode || $fromNode->next() === $toNode || $fromNode->next() === null || $toNode->previous() === null) {
            return;
        }

        self::mergeTextNodesInclusive($fromNode->next(), $toNode->previous());
    }

    private static function mergeTextNodesInclusive(Node $fromNode, Node $toNode): void
    {
        $first = null;
        $last = null;

        $node = $fromNode;
        while ($node !== null) {
            if ($node instanceof Text) {
                if ($first === null) {
                    $first = $node;
                }
                $last = $node;
            } else {
                self::mergeIfNeeded($first, $last);
                $first = null;
                $last = null;
            }
            if ($node === $toNode) {
                break;
            }
            $node = $node->next();
        }

        self::mergeIfNeeded($first, $last);
    }

    private static function mergeIfNeeded(?Text $first, ?Text $last): void
    {
        if ($first === null || $last === null || $first === $last) {
            // No merging needed
            return;
        }

        $s = $first->getContent();

        $node = $first->next();
        $stop = $last->next();
        while ($node !== $stop && $node instanceof Text) {
            $s .= $node->getContent();
            $unlink = $node;
            $node = $node->next();
            $unlink->detach();
        }

        $first->setContent($s);
    }
}
