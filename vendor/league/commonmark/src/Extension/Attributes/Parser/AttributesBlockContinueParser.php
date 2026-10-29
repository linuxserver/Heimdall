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

namespace League\CommonMark\Extension\Attributes\Parser;

use League\CommonMark\Extension\Attributes\Node\Attributes;
use League\CommonMark\Extension\Attributes\Util\AttributesHelper;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Parser\Block\AbstractBlockContinueParser;
use League\CommonMark\Parser\Block\BlockContinue;
use League\CommonMark\Parser\Block\BlockContinueParserInterface;
use League\CommonMark\Parser\Cursor;

final class AttributesBlockContinueParser extends AbstractBlockContinueParser
{
    private Attributes $block;

    private AbstractBlock $container;

    private bool $hasSubsequentLine = false;

    /**
     * Every attribute merged so far except the class list, which is accumulated separately and
     * only takes the placeholder position recorded here. Re-merging the whole dict on every line
     * would cost O(size of the dict) per line, so a document adding a key per line would be
     * quadratic; folding each line in on its own is linear.
     *
     * @var array<string, mixed>
     */
    private array $attributes = [];

    /** @var list<string> */
    private array $classes = [];

    private bool $hasClass = false;

    /**
     * mergeAttributes() rebuilds the class list before anything else, so any line merged over an
     * existing list moves it to the front of the result
     */
    private bool $classFirst = false;

    /**
     * @param array<string, mixed> $attributes The attributes identified by the block start parser
     * @param AbstractBlock        $container  The node we were in when these attributes were discovered
     */
    public function __construct(array $attributes, AbstractBlock $container)
    {
        $this->block = new Attributes($attributes);

        $this->container = $container;

        $this->absorb($attributes);
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function tryContinue(Cursor $cursor, BlockContinueParserInterface $activeBlockParser): ?BlockContinue
    {
        $this->hasSubsequentLine = true;

        $cursor->advanceToNextNonSpaceOrTab();

        // Does this next line also have attributes?
        $attributes = AttributesHelper::parseAttributes($cursor);
        $cursor->advanceToNextNonSpaceOrTab();
        if ($cursor->isAtEnd() && $attributes !== []) {
            // It does! Merge them into what we parsed previously
            $this->classFirst = $this->classFirst || $this->hasClass;

            $this->absorb($attributes);

            // Tell the core parser we've consumed everything
            return BlockContinue::at($cursor);
        }

        // Okay, so there are no attributes on the next line
        // If this next line is blank we know we can't target the next node, it must be a previous one
        if ($cursor->isBlank()) {
            $this->block->setTarget(Attributes::TARGET_PREVIOUS);
        }

        return BlockContinue::none();
    }

    /**
     * Fold one line's attributes into what we've accumulated, in time proportional to that line
     *
     * @param array<string, mixed> $attributes
     */
    private function absorb(array $attributes): void
    {
        foreach ($attributes as $name => $value) {
            if ($name !== 'class') {
                $this->attributes[$name] = $value;

                continue;
            }

            foreach (AttributesHelper::classList($value) as $class) {
                $this->classes[] = $class;
            }

            if ($this->hasClass) {
                continue;
            }

            // Hold the spot the joined list will occupy; closeBlock() writes the value
            $this->attributes['class'] = null;
            $this->hasClass            = true;
        }
    }

    public function closeBlock(): void
    {
        $attributes = $this->attributes;
        if ($this->hasClass) {
            $class = \implode(' ', $this->classes);
            if ($this->classFirst) {
                unset($attributes['class']);
                $attributes = \array_merge(['class' => $class], $attributes);
            } else {
                $attributes['class'] = $class;
            }
        }

        $this->block->setAttributes($attributes);

        // Attributes appearing at the very end of the document won't have any last lines to check
        // so we can make that determination here
        if (! $this->hasSubsequentLine) {
            $this->block->setTarget(Attributes::TARGET_PREVIOUS);
        }

        // We know this block must apply to the "previous" block, but that could be a sibling or parent,
        // so we check the containing block to see which one it might be.
        if ($this->block->getTarget() === Attributes::TARGET_PREVIOUS && $this->block->parent() === $this->container) {
            $this->block->setTarget(Attributes::TARGET_PARENT);
        }
    }
}
