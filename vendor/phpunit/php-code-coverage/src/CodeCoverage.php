<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage;

use function array_diff;
use function array_diff_key;
use function array_flip;
use function array_keys;
use function array_merge;
use function array_unique;
use function count;
use function explode;
use function is_file;
use function sort;
use ReflectionClass;
use SebastianBergmann\CodeCoverage\Data\ProcessedCodeCoverageData;
use SebastianBergmann\CodeCoverage\Data\RawCodeCoverageData;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use SebastianBergmann\CodeCoverage\Node\Builder;
use SebastianBergmann\CodeCoverage\Node\Directory;
use SebastianBergmann\CodeCoverage\StaticAnalysis\CachingSourceAnalyser;
use SebastianBergmann\CodeCoverage\StaticAnalysis\FileAnalyser;
use SebastianBergmann\CodeCoverage\StaticAnalysis\ParsingSourceAnalyser;
use SebastianBergmann\CodeCoverage\Test\Target\MapBuilder;
use SebastianBergmann\CodeCoverage\Test\Target\Mapper;
use SebastianBergmann\CodeCoverage\Test\Target\TargetCollection;
use SebastianBergmann\CodeCoverage\Test\Target\TargetCollectionValidator;
use SebastianBergmann\CodeCoverage\Test\Target\ValidationResult;
use SebastianBergmann\CodeCoverage\Test\TestSize\TestSize;
use SebastianBergmann\CodeCoverage\Test\TestStatus\TestStatus;

/**
 * Provides collection functionality for PHP code coverage information.
 *
 * @phpstan-type TestType array{size: string, status: string, time: float}
 * @phpstan-type TargetedLines array<non-empty-string, list<positive-int>>
 */
final class CodeCoverage
{
    private const string UNCOVERED_FILES = 'UNCOVERED_FILES';
    private readonly Driver $driver;
    private readonly Filter $filter;
    private ?FileAnalyser $analyser                  = null;
    private ?Mapper $targetMapper                    = null;
    private ?string $cacheDirectory                  = null;
    private bool $checkForUnintentionallyCoveredCode = false;
    private bool $collectBranchAndPathCoverage       = false;
    private bool $includeUncoveredFiles              = true;
    private bool $ignoreDeprecatedCode               = false;
    private bool $useAnnotationsForIgnoringCode      = true;

    /**
     * @var list<class-string>
     */
    private array $parentClassesExcludedFromUnintentionallyCoveredCodeCheck = [];
    private ?string $currentId                                              = null;
    private ?TestSize $currentSize                                          = null;
    private ProcessedCodeCoverageData $data;

    /**
     * @var array<string, TestType>
     */
    private array $tests             = [];
    private ?Directory $cachedReport = null;

    public function __construct(Driver $driver, Filter $filter)
    {
        $this->driver = $driver;
        $this->filter = $filter;
        $this->data   = new ProcessedCodeCoverageData;
    }

    public function __serialize(): array
    {
        $prefix = "\x00" . self::class . "\x00";

        return [
            // Configuration
            $prefix . 'cacheDirectory'                                           => $this->cacheDirectory,
            $prefix . 'checkForUnintentionallyCoveredCode'                       => $this->checkForUnintentionallyCoveredCode,
            $prefix . 'includeUncoveredFiles'                                    => $this->includeUncoveredFiles,
            $prefix . 'ignoreDeprecatedCode'                                     => $this->ignoreDeprecatedCode,
            $prefix . 'parentClassesExcludedFromUnintentionallyCoveredCodeCheck' => $this->parentClassesExcludedFromUnintentionallyCoveredCodeCheck,
            $prefix . 'useAnnotationsForIgnoringCode'                            => $this->useAnnotationsForIgnoringCode,
            $prefix . 'filter'                                                   => $this->filter,

            // Data
            $prefix . 'data'  => $this->data,
            $prefix . 'tests' => $this->tests,
        ];
    }

    /**
     * Returns the code coverage information as a graph of node objects.
     */
    public function getReport(): Directory
    {
        if ($this->cachedReport === null) {
            $this->cachedReport = (new Builder($this->analyser()))->build($this);
        }

        return $this->cachedReport;
    }

    /**
     * Clears collected code coverage data.
     */
    public function clear(): void
    {
        $this->currentId    = null;
        $this->currentSize  = null;
        $this->data         = new ProcessedCodeCoverageData;
        $this->tests        = [];
        $this->cachedReport = null;
    }

    /**
     * @internal
     */
    public function clearCache(): void
    {
        $this->cachedReport = null;
    }

    /**
     * Returns the filter object used.
     */
    public function filter(): Filter
    {
        return $this->filter;
    }

    /**
     * Returns the collected code coverage data.
     */
    public function getData(bool $raw = false): ProcessedCodeCoverageData
    {
        if (!$raw) {
            if ($this->includeUncoveredFiles) {
                $this->addUncoveredFilesFromFilter();
            }
        }

        return $this->data;
    }

    /**
     * Sets the coverage data.
     */
    public function setData(ProcessedCodeCoverageData $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array<string, TestType>
     */
    public function getTests(): array
    {
        return $this->tests;
    }

    /**
     * @param array<string, TestType> $tests
     */
    public function setTests(array $tests): void
    {
        $this->tests = $tests;
    }

    public function start(string $id, ?TestSize $size = null, bool $clear = false): void
    {
        if ($clear) {
            $this->clear();
        }

        $this->currentId   = $id;
        $this->currentSize = $size;

        $this->driver->start();

        $this->cachedReport = null;
    }

    public function stop(bool $append = true, ?TestStatus $status = null, null|false|TargetCollection $covers = null, ?TargetCollection $uses = null, float $time = 0.0): RawCodeCoverageData
    {
        $data = $this->driver->stop();

        $this->append($data, null, $append, $status, $covers, $uses, $time);

        $this->currentId    = null;
        $this->currentSize  = null;
        $this->cachedReport = null;

        return $data;
    }

    /**
     * @throws ReflectionException
     * @throws TestIdMissingException
     * @throws UnintentionallyCoveredCodeException
     */
    public function append(RawCodeCoverageData $rawData, ?string $id = null, bool $append = true, ?TestStatus $status = null, null|false|TargetCollection $covers = null, ?TargetCollection $uses = null, float $time = 0.0): void
    {
        if ($id === null) {
            $id = $this->currentId;
        }

        if ($id === null) {
            throw new TestIdMissingException;
        }

        if ($status === null) {
            $status = TestStatus::unknown();
        }

        if ($covers === null) {
            $covers = TargetCollection::fromArray([]);
        }

        if ($uses === null) {
            $uses = TargetCollection::fromArray([]);
        }

        $size = $this->currentSize;

        if ($size === null) {
            $size = TestSize::unknown();
        }

        $this->cachedReport = null;

        $this->applyFilter($rawData);

        $this->applyExecutableLinesFilter($rawData);

        if ($this->useAnnotationsForIgnoringCode) {
            $this->applyIgnoredLinesFilter($rawData);
        }

        $this->data->initializeUnseenData($rawData);

        if (!$append) {
            return;
        }

        if ($id === self::UNCOVERED_FILES) {
            return;
        }

        $linesToBeCovered = false;
        $linesToBeUsed    = [];

        if ($covers !== false) {
            $linesToBeCovered = $this->targetMapper()->mapTargets($covers);
        }

        if ($linesToBeCovered !== false) {
            $linesToBeUsed = $this->targetMapper()->mapTargets($uses);
        }

        $this->applyCoversAndUsesFilter(
            $rawData,
            $linesToBeCovered,
            $linesToBeUsed,
            $size,
        );

        if ($rawData->lineCoverage() === []) {
            return;
        }

        $this->tests[$id] = [
            'size'   => $size->asString(),
            'status' => $status->asString(),
            'time'   => $time,
        ];

        $this->data->markCodeAsExecutedByTestCase($id, $rawData);
    }

    /**
     * Merges the data from another instance.
     */
    public function merge(self $that): void
    {
        $this->filter->includeFiles(
            $that->filter()->files(),
        );

        $this->data->merge($that->data);

        $this->tests = array_merge($this->tests, $that->getTests());

        $this->cachedReport = null;
    }

    public function enableCheckForUnintentionallyCoveredCode(): void
    {
        $this->checkForUnintentionallyCoveredCode = true;
    }

    public function disableCheckForUnintentionallyCoveredCode(): void
    {
        $this->checkForUnintentionallyCoveredCode = false;
    }

    public function includeUncoveredFiles(): void
    {
        $this->includeUncoveredFiles = true;
    }

    public function excludeUncoveredFiles(): void
    {
        $this->includeUncoveredFiles = false;
    }

    public function enableAnnotationsForIgnoringCode(): void
    {
        $this->useAnnotationsForIgnoringCode = true;
    }

    public function disableAnnotationsForIgnoringCode(): void
    {
        $this->useAnnotationsForIgnoringCode = false;
    }

    public function ignoreDeprecatedCode(): void
    {
        $this->ignoreDeprecatedCode = true;
    }

    public function doNotIgnoreDeprecatedCode(): void
    {
        $this->ignoreDeprecatedCode = false;
    }

    /**
     * @phpstan-assert-if-true !null $this->cacheDirectory
     */
    public function cachesStaticAnalysis(): bool
    {
        return $this->cacheDirectory !== null;
    }

    public function cacheStaticAnalysis(string $directory): void
    {
        $this->cacheDirectory = $directory;
    }

    public function doNotCacheStaticAnalysis(): void
    {
        $this->cacheDirectory = null;
    }

    /**
     * @throws StaticAnalysisCacheNotConfiguredException
     */
    public function cacheDirectory(): string
    {
        if (!$this->cachesStaticAnalysis()) {
            throw new StaticAnalysisCacheNotConfiguredException(
                'The static analysis cache is not configured',
            );
        }

        return $this->cacheDirectory;
    }

    /**
     * @param class-string $className
     */
    public function excludeSubclassesOfThisClassFromUnintentionallyCoveredCodeCheck(string $className): void
    {
        $this->parentClassesExcludedFromUnintentionallyCoveredCodeCheck[] = $className;
    }

    public function enableBranchAndPathCoverage(): void
    {
        $this->driver->enableBranchAndPathCoverage();

        $this->collectBranchAndPathCoverage = true;
    }

    public function disableBranchAndPathCoverage(): void
    {
        $this->driver->disableBranchAndPathCoverage();

        $this->collectBranchAndPathCoverage = false;
    }

    public function collectsBranchAndPathCoverage(): bool
    {
        return $this->collectBranchAndPathCoverage;
    }

    public function validate(TargetCollection $targets): ValidationResult
    {
        return (new TargetCollectionValidator)->validate($this->targetMapper(), $targets);
    }

    /**
     * @internal
     */
    public function driverIsPcov(): bool
    {
        /** @phpstan-ignore isset.initializedProperty */
        return isset($this->driver) && $this->driver->isPcov();
    }

    /**
     * @internal
     */
    public function driverIsXdebug(): bool
    {
        /** @phpstan-ignore isset.initializedProperty */
        return isset($this->driver) && $this->driver->isXdebug();
    }

    /**
     * @param false|TargetedLines $linesToBeCovered
     * @param TargetedLines       $linesToBeUsed
     *
     * @throws ReflectionException
     * @throws UnintentionallyCoveredCodeException
     */
    private function applyCoversAndUsesFilter(RawCodeCoverageData $rawData, array|false $linesToBeCovered, array $linesToBeUsed, TestSize $size): void
    {
        if ($linesToBeCovered === false) {
            $rawData->clear();

            return;
        }

        if ($linesToBeCovered === []) {
            return;
        }

        if ($this->checkForUnintentionallyCoveredCode && !$size->isMedium() && !$size->isLarge()) {
            $this->performUnintentionallyCoveredCodeCheck($rawData, $linesToBeCovered, $linesToBeUsed);
        }

        $rawLineData         = $rawData->lineCoverage();
        $filesWithNoCoverage = array_diff_key($rawLineData, $linesToBeCovered);

        foreach (array_keys($filesWithNoCoverage) as $fileWithNoCoverage) {
            $rawData->removeCoverageDataForFile($fileWithNoCoverage);
        }

        foreach ($linesToBeCovered as $fileToBeCovered => $includedLines) {
            $rawData->keepLineCoverageDataOnlyForLines($fileToBeCovered, $includedLines);
            $rawData->keepFunctionCoverageDataOnlyForLines($fileToBeCovered, $includedLines);
        }
    }

    private function applyFilter(RawCodeCoverageData $data): void
    {
        if (!$this->filter->isEmpty()) {
            foreach (array_keys($data->lineCoverage()) as $filename) {
                if ($this->filter->isExcluded($filename)) {
                    $data->removeCoverageDataForFile($filename);
                }
            }
        }

        $data->skipEmptyLines();
    }

    private function applyExecutableLinesFilter(RawCodeCoverageData $data): void
    {
        foreach (array_keys($data->lineCoverage()) as $filename) {
            if (!$this->filter->isFile($filename)) {
                continue;
            }

            $linesToBranchMap = $this->analyser()->analyse($filename)->executableLines();

            $data->keepLineCoverageDataOnlyForLines(
                $filename,
                array_keys($linesToBranchMap),
            );

            $data->markExecutableLineByBranch(
                $filename,
                $linesToBranchMap,
            );
        }
    }

    private function applyIgnoredLinesFilter(RawCodeCoverageData $data): void
    {
        foreach (array_keys($data->lineCoverage()) as $filename) {
            if (!$this->filter->isFile($filename)) {
                continue;
            }

            $data->removeCoverageDataForLines(
                $filename,
                $this->analyser()->analyse($filename)->ignoredLines(),
            );
        }
    }

    /**
     * @throws UnintentionallyCoveredCodeException
     */
    private function addUncoveredFilesFromFilter(): void
    {
        $uncoveredFiles = array_diff(
            $this->filter->files(),
            $this->data->coveredFiles(),
        );

        foreach ($uncoveredFiles as $uncoveredFile) {
            if (is_file($uncoveredFile)) {
                $this->append(
                    RawCodeCoverageData::fromUncoveredFile(
                        $uncoveredFile,
                        $this->analyser(),
                    ),
                    self::UNCOVERED_FILES,
                );
            }
        }
    }

    /**
     * @param TargetedLines $linesToBeCovered
     * @param TargetedLines $linesToBeUsed
     *
     * @throws ReflectionException
     * @throws UnintentionallyCoveredCodeException
     */
    private function performUnintentionallyCoveredCodeCheck(RawCodeCoverageData $data, array $linesToBeCovered, array $linesToBeUsed): void
    {
        $allowedLines = $this->getAllowedLines(
            $linesToBeCovered,
            $linesToBeUsed,
        );

        $unintentionallyCoveredUnits = [];

        foreach ($data->lineCoverage() as $file => $_data) {
            foreach ($_data as $line => $flag) {
                if ($flag === 1 && !isset($allowedLines[$file][$line])) {
                    $unintentionallyCoveredUnits[] = $this->targetMapper->lookup($file, $line);
                }
            }
        }

        $unintentionallyCoveredUnits = $this->processUnintentionallyCoveredUnits($unintentionallyCoveredUnits);

        if ($unintentionallyCoveredUnits !== []) {
            throw new UnintentionallyCoveredCodeException(
                $unintentionallyCoveredUnits,
            );
        }
    }

    /**
     * @param TargetedLines $linesToBeCovered
     * @param TargetedLines $linesToBeUsed
     *
     * @return TargetedLines
     */
    private function getAllowedLines(array $linesToBeCovered, array $linesToBeUsed): array
    {
        $allowedLines = [];

        foreach (array_keys($linesToBeCovered) as $file) {
            if (!isset($allowedLines[$file])) {
                $allowedLines[$file] = [];
            }

            $allowedLines[$file] = array_merge(
                $allowedLines[$file],
                $linesToBeCovered[$file],
            );
        }

        foreach (array_keys($linesToBeUsed) as $file) {
            if (!isset($allowedLines[$file])) {
                $allowedLines[$file] = [];
            }

            $allowedLines[$file] = array_merge(
                $allowedLines[$file],
                $linesToBeUsed[$file],
            );
        }

        foreach (array_keys($allowedLines) as $file) {
            $allowedLines[$file] = array_flip(
                array_unique($allowedLines[$file]),
            );
        }

        return $allowedLines;
    }

    /**
     * @param list<string> $unintentionallyCoveredUnits
     *
     * @throws ReflectionException
     *
     * @return list<string>
     */
    private function processUnintentionallyCoveredUnits(array $unintentionallyCoveredUnits): array
    {
        $unintentionallyCoveredUnits = array_unique($unintentionallyCoveredUnits);
        $processed                   = [];

        foreach ($unintentionallyCoveredUnits as $unintentionallyCoveredUnit) {
            $tmp = explode('::', $unintentionallyCoveredUnit);

            if (count($tmp) !== 2) {
                $processed[] = $unintentionallyCoveredUnit;

                continue;
            }

            try {
                $class = new ReflectionClass($tmp[0]);

                foreach ($this->parentClassesExcludedFromUnintentionallyCoveredCodeCheck as $parentClass) {
                    if ($class->isSubclassOf($parentClass)) {
                        continue 2;
                    }
                }
            } catch (\ReflectionException $e) {
                throw new ReflectionException(
                    $e->getMessage(),
                    $e->getCode(),
                    $e,
                );
            }

            $processed[] = $tmp[0];
        }

        $processed = array_unique($processed);

        sort($processed);

        return $processed;
    }

    private function targetMapper(): Mapper
    {
        if ($this->targetMapper !== null) {
            return $this->targetMapper;
        }

        $this->targetMapper = new Mapper(
            (new MapBuilder)->build($this->filter, $this->analyser()),
        );

        return $this->targetMapper;
    }

    private function analyser(): FileAnalyser
    {
        if ($this->analyser !== null) {
            return $this->analyser;
        }

        $sourceAnalyser = new ParsingSourceAnalyser;

        if ($this->cachesStaticAnalysis()) {
            $sourceAnalyser = new CachingSourceAnalyser(
                $this->cacheDirectory,
                $sourceAnalyser,
            );
        }

        $this->analyser = new FileAnalyser(
            $sourceAnalyser,
            $this->useAnnotationsForIgnoringCode,
            $this->ignoreDeprecatedCode,
        );

        return $this->analyser;
    }
}
