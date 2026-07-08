<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\StaticAnalysis;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final readonly class Interface_
{
    /**
     * @var non-empty-string
     */
    private string $name;

    /**
     * @var non-empty-string
     */
    private string $namespacedName;
    private string $namespace;

    /**
     * @var non-negative-int
     */
    private int $startLine;

    /**
     * @var non-negative-int
     */
    private int $endLine;

    /**
     * @var list<non-empty-string>
     */
    private array $parentInterfaces;

    /**
     * @param non-empty-string       $name
     * @param non-empty-string       $namespacedName
     * @param non-negative-int       $startLine
     * @param non-negative-int       $endLine
     * @param list<non-empty-string> $parentInterfaces
     */
    public function __construct(string $name, string $namespacedName, string $namespace, int $startLine, int $endLine, array $parentInterfaces)
    {
        $this->name             = $name;
        $this->namespacedName   = $namespacedName;
        $this->namespace        = $namespace;
        $this->startLine        = $startLine;
        $this->endLine          = $endLine;
        $this->parentInterfaces = $parentInterfaces;
    }

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return non-empty-string
     */
    public function namespacedName(): string
    {
        return $this->namespacedName;
    }

    public function isNamespaced(): bool
    {
        return $this->namespace !== '';
    }

    public function namespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return non-negative-int
     */
    public function startLine(): int
    {
        return $this->startLine;
    }

    /**
     * @return non-negative-int
     */
    public function endLine(): int
    {
        return $this->endLine;
    }

    /**
     * @return list<non-empty-string>
     */
    public function parentInterfaces(): array
    {
        return $this->parentInterfaces;
    }
}
