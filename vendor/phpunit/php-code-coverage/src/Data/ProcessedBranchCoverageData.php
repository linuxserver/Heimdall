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

use function array_merge;
use function array_unique;
use NoDiscard;
use SebastianBergmann\CodeCoverage\Driver\XdebugDriver;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 *
 * @phpstan-import-type TestIdType from ProcessedCodeCoverageData
 * @phpstan-import-type XdebugBranchCoverageType from XdebugDriver
 */
final class ProcessedBranchCoverageData
{
    public readonly int $op_start;
    public readonly int $op_end;
    public readonly int $line_start;
    public readonly int $line_end;

    /** @var list<TestIdType> */
    public array $hit;

    /** @var array<int, int> */
    public readonly array $out;

    /** @var array<int, int> */
    public readonly array $out_hit;

    /**
     * @param XdebugBranchCoverageType $xdebugCoverageData
     */
    public static function fromXdebugCoverage(array $xdebugCoverageData): self
    {
        return new self(
            $xdebugCoverageData['op_start'],
            $xdebugCoverageData['op_end'],
            $xdebugCoverageData['line_start'],
            $xdebugCoverageData['line_end'],
            [],
            $xdebugCoverageData['out'],
            $xdebugCoverageData['out_hit'],
        );
    }

    /**
     * @param list<TestIdType> $hit
     * @param array<int, int>  $out
     * @param array<int, int>  $out_hit
     */
    public function __construct(
        int $op_start,
        int $op_end,
        int $line_start,
        int $line_end,
        array $hit,
        array $out,
        array $out_hit,
    ) {
        $this->out_hit    = $out_hit;
        $this->out        = $out;
        $this->hit        = $hit;
        $this->line_end   = $line_end;
        $this->line_start = $line_start;
        $this->op_end     = $op_end;
        $this->op_start   = $op_start;
    }

    #[NoDiscard]
    public function merge(self $data): self
    {
        if ($data->hit === []) {
            return $this;
        }

        return new self(
            $this->op_start,
            $this->op_end,
            $this->line_start,
            $this->line_end,
            array_unique(array_merge($this->hit, $data->hit)),
            $this->out,
            $this->out_hit,
        );
    }

    /**
     * @param TestIdType $testCaseId
     */
    public function recordHit(string $testCaseId): void
    {
        $this->hit[] = $testCaseId;
    }
}
