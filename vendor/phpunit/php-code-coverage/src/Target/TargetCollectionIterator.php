<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Test\Target;

use function count;
use Iterator;

/**
 * @template-implements Iterator<int, Target>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class TargetCollectionIterator implements Iterator
{
    /**
     * @var list<Target>
     */
    private readonly array $targets;
    private int $position = 0;

    public function __construct(TargetCollection $metadata)
    {
        $this->targets = $metadata->asArray();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->position < count($this->targets);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function current(): Target
    {
        return $this->targets[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }
}
