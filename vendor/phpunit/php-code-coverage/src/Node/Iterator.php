<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Node;

use function assert;
use function count;
use RecursiveIterator;

/**
 * @template-implements RecursiveIterator<int, AbstractNode>
 *
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Iterator implements RecursiveIterator
{
    private int $position;

    /**
     * @var list<AbstractNode>
     */
    private readonly array $nodes;

    public function __construct(Directory $node)
    {
        $this->nodes = $node->children();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->position < count($this->nodes);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function current(): ?AbstractNode
    {
        return $this->valid() ? $this->nodes[$this->position] : null;
    }

    public function next(): void
    {
        $this->position++;
    }

    public function getChildren(): self
    {
        assert($this->nodes[$this->position] instanceof Directory);

        return new self($this->nodes[$this->position]);
    }

    public function hasChildren(): bool
    {
        return $this->nodes[$this->position] instanceof Directory;
    }
}
