<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\NormalizeHeadings;

use League\CommonMark\Environment\EnvironmentAwareInterface;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\NodeIterator;
use League\Config\ConfigurationInterface;

final class NormalizeHeadingsProcessor implements EnvironmentAwareInterface
{
    /** @psalm-readonly-allow-private-mutation */
    private ConfigurationInterface $config;

    public function setEnvironment(EnvironmentInterface $environment): void
    {
        $this->config = $environment->getConfiguration();
    }

    public function __invoke(DocumentParsedEvent $event): void
    {
        $minLevel         = (int) $this->config->get('normalize_headings/min_level');
        $maxLevel         = (int) $this->config->get('normalize_headings/max_level');
        $rebaseToMinLevel = (bool) $this->config->get('normalize_headings/rebase_to_min_level');

        /**
         * The headings the current one is nested within, tracked by both their original level (which
         * determines that nesting) and their new level (which limits how far the current one may descend)
         *
         * @var array<int, array{original: int, output: int}> $ancestors
         */
        $ancestors = [];

        foreach ($event->getDocument()->iterator(NodeIterator::FLAG_BLOCKS_ONLY) as $node) {
            if (! $node instanceof Heading) {
                continue;
            }

            $level = $node->getLevel();

            // Pop any headings this one isn't nested within - they're siblings or cousins, not ancestors
            $parent = \end($ancestors);
            while ($parent !== false && $level <= $parent['original']) {
                \array_pop($ancestors);
                $parent = \end($ancestors);
            }

            if ($parent === false) {
                $newLevel = $rebaseToMinLevel ? $minLevel : self::clamp($level, $minLevel, $maxLevel);
            } else {
                // Place this heading exactly one level below its parent, regardless of the level it was
                // written at - unless that would exceed the maximum, in which case it becomes a sibling
                $newLevel = \min($parent['output'] + 1, $maxLevel);
            }

            $node->setLevel($newLevel);
            $ancestors[] = ['original' => $level, 'output' => $newLevel];
        }
    }

    private static function clamp(int $level, int $minLevel, int $maxLevel): int
    {
        return \max($minLevel, \min($level, $maxLevel));
    }
}
