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

namespace League\CommonMark\Normalizer;

// phpcs:disable Squiz.Strings.DoubleQuoteUsage.ContainsVar
final class UniqueSlugNormalizer implements UniqueSlugNormalizerInterface
{
    private TextNormalizerInterface $innerNormalizer;

    /**
     * Slugs claimed by the surrounding page (in final, normalized form), seeded as if they'd
     * already been handed out once, so the first colliding slug gets a "-1" suffix.
     * Unlike regular history, these survive clearHistory().
     *
     * @var array<string, int>
     */
    private array $reserved = [];

    /**
     * Every slug we've handed out, mapped to the next numeric suffix to try for it
     *
     * @var array<string, int>
     */
    private array $alreadyUsed;

    /**
     * @param iterable<string> $reservedSlugs
     */
    public function __construct(TextNormalizerInterface $innerNormalizer, iterable $reservedSlugs = [])
    {
        $this->innerNormalizer = $innerNormalizer;

        foreach ($reservedSlugs as $slug) {
            $this->reserved[$slug] = 1;
        }

        $this->alreadyUsed = $this->reserved;
    }

    public function clearHistory(): void
    {
        $this->alreadyUsed = $this->reserved;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-allow-private-mutation
     */
    public function normalize(string $text, array $context = []): string
    {
        $normalized = $this->innerNormalizer->normalize($text, $context);

        // If it's not unique, add an incremental number to the end until we get a unique version.
        // Suffixes are handed out in ascending order and are never given back, so we can pick up
        // where the previous collision left off instead of re-checking suffixes we know are taken.
        if (isset($this->alreadyUsed[$normalized])) {
            $suffix = $this->alreadyUsed[$normalized];
            while (isset($this->alreadyUsed["$normalized-$suffix"])) {
                ++$suffix;
            }

            $this->alreadyUsed[$normalized] = $suffix + 1;

            $normalized = "$normalized-$suffix";
        }

        $this->alreadyUsed[$normalized] = 1;

        return $normalized;
    }
}
