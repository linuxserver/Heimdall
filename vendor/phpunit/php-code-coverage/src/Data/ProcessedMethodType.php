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
final class ProcessedMethodType
{
    public readonly string $methodName;
    public readonly string $visibility;
    public readonly string $signature;
    public readonly int $startLine;
    public readonly int $endLine;
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
        string $methodName,
        string $visibility,
        string $signature,
        int $startLine,
        int $endLine,
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
        $this->endLine            = $endLine;
        $this->startLine          = $startLine;
        $this->signature          = $signature;
        $this->visibility         = $visibility;
        $this->methodName         = $methodName;
    }
}
