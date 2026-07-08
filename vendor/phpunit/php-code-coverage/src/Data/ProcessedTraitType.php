<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Data;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class ProcessedTraitType
{
    public readonly string $traitName;
    public readonly string $namespace;

    /**
     * @var array<string, ProcessedMethodType>
     */
    public array $methods;
    public readonly int $startLine;
    public int $executableLines;
    public int $executedLines;
    public int $executableBranches;
    public int $executedBranches;
    public int $executablePaths;
    public int $executedPaths;
    public int $ccn;
    public float|int $coverage;
    public int|string $crap;
    public readonly string $link;

    public function __construct(
        string $traitName,
        string $namespace,
        /**
         * @var array<string, ProcessedMethodType>
         */
        array $methods,
        int $startLine,
        int $executableLines,
        int $executedLines,
        int $executableBranches,
        int $executedBranches,
        int $executablePaths,
        int $executedPaths,
        int $ccn,
        float|int $coverage,
        int|string $crap,
        string $link,
    ) {
        $this->link               = $link;
        $this->crap               = $crap;
        $this->coverage           = $coverage;
        $this->ccn                = $ccn;
        $this->executedPaths      = $executedPaths;
        $this->executablePaths    = $executablePaths;
        $this->executedBranches   = $executedBranches;
        $this->executableBranches = $executableBranches;
        $this->executedLines      = $executedLines;
        $this->executableLines    = $executableLines;
        $this->startLine          = $startLine;
        $this->methods            = $methods;
        $this->namespace          = $namespace;
        $this->traitName          = $traitName;
    }
}
