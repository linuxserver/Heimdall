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
final readonly class Function_
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
     * @var non-empty-string
     */
    private string $signature;

    /**
     * @var positive-int
     */
    private int $cyclomaticComplexity;

    /**
     * @param non-empty-string $name
     * @param non-empty-string $namespacedName
     * @param non-negative-int $startLine
     * @param non-negative-int $endLine
     * @param non-empty-string $signature
     * @param positive-int     $cyclomaticComplexity
     */
    public function __construct(string $name, string $namespacedName, string $namespace, int $startLine, int $endLine, string $signature, int $cyclomaticComplexity)
    {
        $this->name                 = $name;
        $this->namespacedName       = $namespacedName;
        $this->namespace            = $namespace;
        $this->startLine            = $startLine;
        $this->endLine              = $endLine;
        $this->signature            = $signature;
        $this->cyclomaticComplexity = $cyclomaticComplexity;
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
     * @return non-empty-string
     */
    public function signature(): string
    {
        return $this->signature;
    }

    /**
     * @return positive-int
     */
    public function cyclomaticComplexity(): int
    {
        return $this->cyclomaticComplexity;
    }
}
