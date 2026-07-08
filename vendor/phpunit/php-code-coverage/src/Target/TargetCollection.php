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
use Countable;
use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<int, Target>
 *
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final readonly class TargetCollection implements Countable, IteratorAggregate
{
    /**
     * @var list<Target>
     */
    private array $targets;

    /**
     * @param list<Target> $targets
     */
    public static function fromArray(array $targets): self
    {
        return new self(...$targets);
    }

    private function __construct(Target ...$targets)
    {
        $this->targets = $targets;
    }

    /**
     * @return list<Target>
     */
    public function asArray(): array
    {
        return $this->targets;
    }

    public function count(): int
    {
        return count($this->targets);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return $this->count() > 0;
    }

    public function getIterator(): TargetCollectionIterator
    {
        return new TargetCollectionIterator($this);
    }
}
