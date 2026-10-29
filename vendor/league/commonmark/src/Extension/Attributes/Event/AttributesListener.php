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

namespace League\CommonMark\Extension\Attributes\Event;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\Attributes\Node\Attributes;
use League\CommonMark\Extension\Attributes\Node\AttributesInline;
use League\CommonMark\Extension\Attributes\Util\AttributesHelper;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Node\Node;

/**
 * @psalm-type PendingAttributes = array{node: Node, front: array<string, mixed>, back: array<string, mixed>, classFront: list<string>, classBack: list<string>, hasClass: bool, unfiltered: array<string, mixed>}
 *
 * @phpstan-type PendingAttributes = array{node: Node, front: array<string, mixed>, back: array<string, mixed>, classFront: list<string>, classBack: list<string>, hasClass: bool, unfiltered: array<string, mixed>}
 */
final class AttributesListener
{
    private const DIRECTION_PREFIX = 'prefix';
    private const DIRECTION_SUFFIX = 'suffix';

    /** @var list<string> */
    private array $allowList;
    private bool $allowUnsafeLinks;

    /**
     * @param list<string> $allowList
     */
    public function __construct(array $allowList = [], bool $allowUnsafeLinks = true)
    {
        $this->allowList        = $allowList;
        $this->allowUnsafeLinks = $allowUnsafeLinks;
    }

    public function processDocument(DocumentParsedEvent $event): void
    {
        // Targets already worked out for attribute blocks we walked past; see findTargetAndDirection()
        /** @var \SplObjectStorage<Attributes|AttributesInline, array<Node|string|null>> $resolved */
        $resolved = new \SplObjectStorage();

        /**
         * Attributes waiting to be written to their target, keyed by target object ID; see
         * newPendingEntry() for the shape and contribute() for how each node is folded in.
         *
         * @var array<int, PendingAttributes> $pending
         */
        $pending = [];

        foreach ($event->getDocument()->iterator() as $node) {
            if (! ($node instanceof Attributes || $node instanceof AttributesInline)) {
                continue;
            }

            [$target, $direction] = self::findTargetAndDirection($node, $resolved);

            if ($target instanceof Node) {
                $parent = $target->parent();
                if ($parent instanceof ListItem && $parent->parent() instanceof ListBlock && $parent->parent()->isTight()) {
                    $target = $parent;
                }

                $id = \spl_object_id($target);
                if (! isset($pending[$id])) {
                    $pending[$id] = self::newPendingEntry($target);
                }

                $this->contribute($pending[$id], $node->getAttributes(), $direction === self::DIRECTION_SUFFIX);
            }

            $node->detach();
        }

        foreach ($pending as $entry) {
            $entry['node']->data->set('attributes', self::assemble($entry));
        }
    }

    /**
     * Seed an accumulator from whatever the target already carries
     *
     * Anything another extension put there takes part in the merges, so it faces the filter too -
     * but only once the first node has had its say, since whatever that node overwrites never
     * reaches the filter at all.
     *
     * @return PendingAttributes
     */
    private static function newPendingEntry(Node $target): array
    {
        /** @var array<string, mixed> $existing */
        $existing = (array) $target->data->get('attributes');

        $entry = ['node' => $target, 'front' => [], 'back' => [], 'classFront' => [], 'classBack' => [], 'hasClass' => false, 'unfiltered' => $existing];

        foreach ($existing as $name => $value) {
            if ($name === 'class') {
                // mergeAttributes() builds the class list by appending to it, so a value with no
                // classes in it - an empty string, or an empty array - never creates the key at
                // all, and leaves the target with no class rather than an empty one
                $classes = AttributesHelper::classList($value);
                if ($classes === []) {
                    continue;
                }

                $entry['classBack'] = $classes;
                $entry['hasClass']  = true;
            }

            $entry['back'][$name] = $value;
        }

        return $entry;
    }

    /**
     * Fold one node's attributes into the accumulator in time proportional to that node's own
     * size, reproducing what mergeAttributes() plus filterAttributes() would have produced had
     * they been handed the whole accumulated dict.
     *
     * Both of those are O(size of the accumulator), so calling them once per node - as this used
     * to - costs O(n^2) for a document that contributes a new key n times.
     *
     * @param PendingAttributes    $entry
     * @param array<string, mixed> $attributes
     * @param bool                 $suffix     Whether the node's attributes lose conflicts to what is already accumulated
     */
    private function contribute(array &$entry, array $attributes, bool $suffix): void
    {
        $class = $attributes['class'] ?? null;
        unset($attributes['class']);

        // Keys whose value this node decides, and which therefore have yet to face the filter
        $touched = [];

        if ($suffix) {
            // mergeAttributes() rebuilds the class list before anything else, so merging over an
            // accumulator that already has one moves it back to the front
            if ($entry['hasClass']) {
                self::moveToFront($entry, 'class');
            } elseif ($class !== null) {
                $entry['back']['class'] = null;
            }

            if ($class !== null) {
                foreach (AttributesHelper::classList($class) as $c) {
                    $entry['classBack'][] = $c;
                }

                $entry['hasClass'] = true;
                $touched['class']  = $class;
            }

            foreach ($attributes as $name => $value) {
                // Overwriting leaves a key where it already sits, in front or behind
                if (\array_key_exists($name, $entry['front'])) {
                    $entry['front'][$name] = $value;
                } else {
                    $entry['back'][$name] = $value;
                }

                $touched[$name] = $value;
            }

            $this->filter($entry, $touched);

            return;
        }

        // This node goes in front of everything accumulated so far. 'front' is kept in reverse, so
        // the keys most recently moved there are the ones assemble() emits first.
        if ($class === null && $entry['hasClass']) {
            self::moveToFront($entry, 'class');
        }

        foreach (\array_reverse(\array_keys($attributes)) as $name) {
            // A key this node shares with the accumulator takes its position from here, but keeps
            // the value it already had
            if (\array_key_exists($name, $entry['front']) || \array_key_exists($name, $entry['back'])) {
                self::moveToFront($entry, $name);

                continue;
            }

            $entry['front'][$name] = $attributes[$name];
            $touched[$name]        = $attributes[$name];
        }

        if ($class !== null) {
            self::moveToFront($entry, 'class');
            foreach (\array_reverse(AttributesHelper::classList($class)) as $c) {
                $entry['classFront'][] = $c;
            }

            $entry['hasClass'] = true;
            $touched['class']  = $class;
        }

        $this->filter($entry, $touched);
    }

    /**
     * Give a key the frontmost position, keeping whatever value it already had
     *
     * @param PendingAttributes $entry
     */
    private static function moveToFront(array &$entry, string $name): void
    {
        $value = $entry['front'][$name] ?? $entry['back'][$name] ?? null;
        unset($entry['front'][$name], $entry['back'][$name]);

        $entry['front'][$name] = $value;
    }

    /**
     * Drop whichever of the just-decided keys the filter rejects
     *
     * Everything else in the accumulator survived this same filter when it was decided, and the
     * filter judges each name and value on its own, so re-examining any of it would be wasted.
     *
     * @param PendingAttributes    $entry
     * @param array<string, mixed> $touched
     */
    private function filter(array &$entry, array $touched): void
    {
        // Whatever the target arrived with has kept its value through this first merge, so it is
        // being decided now too
        foreach ($entry['unfiltered'] as $name => $value) {
            if (\array_key_exists($name, $touched)) {
                continue;
            }

            $touched[$name] = $value;
        }

        $entry['unfiltered'] = [];

        if ($touched === []) {
            return;
        }

        $kept = AttributesHelper::filterAttributes($touched, $this->allowList, $this->allowUnsafeLinks);

        foreach (\array_keys($touched) as $name) {
            if (\array_key_exists($name, $kept)) {
                continue;
            }

            if ($name === 'class') {
                $entry['classFront'] = $entry['classBack'] = [];
                $entry['hasClass']   = false;
            }

            // Forget where it sat as well as what it held, so a later node contributing the same
            // name starts it over at the back
            unset($entry['front'][$name], $entry['back'][$name]);
        }
    }

    /**
     * @param PendingAttributes $entry
     *
     * @return array<string, mixed>
     */
    private static function assemble(array $entry): array
    {
        $attributes = \array_merge(\array_reverse($entry['front'], true), $entry['back']);

        if ($entry['hasClass']) {
            $attributes['class'] = \implode(' ', \array_merge(\array_reverse($entry['classFront']), $entry['classBack']));
        }

        return $attributes;
    }

    /**
     * @param Attributes|AttributesInline                                             $node
     * @param \SplObjectStorage<Attributes|AttributesInline, array<Node|string|null>> $resolved
     *
     * @return array<Node|string|null>
     */
    private static function findTargetAndDirection($node, \SplObjectStorage $resolved): array
    {
        if (isset($resolved[$node])) {
            $result = $resolved[$node];
            // Each node is only ever resolved once, so don't keep it alive any longer
            unset($resolved[$node]);

            return $result;
        }

        $target    = null;
        $direction = null;
        $previous  = $next = $node;
        /** @var list<Attributes> $shared */
        $shared = [];
        while (true) {
            $previous = self::getPrevious($previous);
            $next     = self::getNext($next);

            if ($previous === null && $next === null) {
                if (! $node->parent() instanceof FencedCode) {
                    $target    = $node->parent();
                    $direction = self::DIRECTION_SUFFIX;
                }

                break;
            }

            if ($node instanceof AttributesInline && ($previous === null || ($previous instanceof AbstractInline && $node->isBlock()))) {
                // Once this condition holds it holds for every remaining iteration, as walking
                // further to the left can only ever yield another inline sibling or null. No
                // sibling can therefore be chosen, so the target must be the parent; continuing
                // to walk would only re-scan the remaining siblings for nothing.
                if (! $node->parent() instanceof FencedCode) {
                    $target    = $node->parent();
                    $direction = self::DIRECTION_SUFFIX;
                }

                break;
            }

            if ($previous !== null && ! self::isAttributesNode($previous)) {
                $target    = $previous;
                $direction = self::DIRECTION_SUFFIX;

                break;
            }

            if ($next !== null && ! self::isAttributesNode($next)) {
                $target    = $next;
                $direction = self::DIRECTION_PREFIX;

                break;
            }

            // Only a TARGET_NEXT block can have a sibling block to walk past, so if that's what
            // we're looking at then we are one too, which means getPrevious() already returned
            // null for us and will keep doing so. Neither of us can therefore pick anything up on
            // the left, and what is left of this walk is the whole of that block's own walk: it
            // must end up with the same target we do. Remember that instead of re-scanning the
            // chain once per block.
            if (! ($next instanceof Attributes) || $next->getTarget() !== Attributes::TARGET_NEXT) {
                continue;
            }

            $shared[] = $next;
        }

        /** @var array<Node|string|null> $result */
        $result = [$target, $direction];
        foreach ($shared as $sharedNode) {
            $resolved[$sharedNode] = $result;
        }

        return $result;
    }

    /**
     * Get any previous block (sibling or parent) this might apply to
     */
    private static function getPrevious(?Node $node = null): ?Node
    {
        if ($node instanceof Attributes) {
            if ($node->getTarget() === Attributes::TARGET_NEXT) {
                return null;
            }

            if ($node->getTarget() === Attributes::TARGET_PARENT) {
                return $node->parent();
            }
        }

        return $node instanceof Node ? $node->previous() : null;
    }

    /**
     * Get any previous block (sibling or parent) this might apply to
     */
    private static function getNext(?Node $node = null): ?Node
    {
        if ($node instanceof Attributes && $node->getTarget() !== Attributes::TARGET_NEXT) {
            return null;
        }

        return $node instanceof Node ? $node->next() : null;
    }

    private static function isAttributesNode(Node $node): bool
    {
        return $node instanceof Attributes || $node instanceof AttributesInline;
    }
}
