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
 * @phpstan-type LinesType array<int, int>
 *
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final readonly class AnalysisResult
{
    /**
     * @var array<string, Interface_>
     */
    private array $interfaces;

    /**
     * @var array<string, Class_>
     */
    private array $classes;

    /**
     * @var array<string, Trait_>
     */
    private array $traits;

    /**
     * @var array<string, Function_>
     */
    private array $functions;
    private LinesOfCode $linesOfCode;

    /**
     * @var LinesType
     */
    private array $executableLines;

    /**
     * @var LinesType
     */
    private array $ignoredLines;

    /**
     * @param array<string, Interface_> $interfaces
     * @param array<string, Class_>     $classes
     * @param array<string, Trait_>     $traits
     * @param array<string, Function_>  $functions
     * @param LinesType                 $executableLines
     * @param LinesType                 $ignoredLines
     */
    public function __construct(array $interfaces, array $classes, array $traits, array $functions, LinesOfCode $linesOfCode, array $executableLines, array $ignoredLines)
    {
        $this->interfaces      = $interfaces;
        $this->classes         = $classes;
        $this->traits          = $traits;
        $this->functions       = $functions;
        $this->linesOfCode     = $linesOfCode;
        $this->executableLines = $executableLines;
        $this->ignoredLines    = $ignoredLines;
    }

    /**
     * @return array<string, Interface_>
     */
    public function interfaces(): array
    {
        return $this->interfaces;
    }

    /**
     * @return array<string, Class_>
     */
    public function classes(): array
    {
        return $this->classes;
    }

    /**
     * @return array<string, Trait_>
     */
    public function traits(): array
    {
        return $this->traits;
    }

    /**
     * @return array<string, Function_>
     */
    public function functions(): array
    {
        return $this->functions;
    }

    public function linesOfCode(): LinesOfCode
    {
        return $this->linesOfCode;
    }

    /**
     * @return LinesType
     */
    public function executableLines(): array
    {
        return $this->executableLines;
    }

    /**
     * @return LinesType
     */
    public function ignoredLines(): array
    {
        return $this->ignoredLines;
    }
}
