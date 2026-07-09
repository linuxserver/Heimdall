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

use function array_filter;
use function count;
use function range;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Data\ProcessedBranchCoverageData;
use SebastianBergmann\CodeCoverage\Data\ProcessedClassType;
use SebastianBergmann\CodeCoverage\Data\ProcessedFunctionType;
use SebastianBergmann\CodeCoverage\Data\ProcessedMethodType;
use SebastianBergmann\CodeCoverage\Data\ProcessedPathCoverageData;
use SebastianBergmann\CodeCoverage\Data\ProcessedTraitType;
use SebastianBergmann\CodeCoverage\StaticAnalysis\AnalysisResult;
use SebastianBergmann\CodeCoverage\StaticAnalysis\Class_;
use SebastianBergmann\CodeCoverage\StaticAnalysis\Function_;
use SebastianBergmann\CodeCoverage\StaticAnalysis\LinesOfCode;
use SebastianBergmann\CodeCoverage\StaticAnalysis\Method;
use SebastianBergmann\CodeCoverage\StaticAnalysis\Trait_;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 *
 * @phpstan-import-type TestType from CodeCoverage
 * @phpstan-import-type LinesType from AnalysisResult
 */
final class File extends AbstractNode
{
    /**
     * @var non-empty-string
     */
    private string $sha1;

    /**
     * @var array<int, ?list<non-empty-string>>
     */
    private array $lineCoverageData;
    private array $functionCoverageData;

    /**
     * @var array<string, TestType>
     */
    private readonly array $testData;
    private int $numExecutableLines    = 0;
    private int $numExecutedLines      = 0;
    private int $numExecutableBranches = 0;
    private int $numExecutedBranches   = 0;
    private int $numExecutablePaths    = 0;
    private int $numExecutedPaths      = 0;

    /**
     * @var array<string, ProcessedClassType>
     */
    private array $classes = [];

    /**
     * @var array<string, ProcessedTraitType>
     */
    private array $traits = [];

    /**
     * @var array<string, ProcessedFunctionType>
     */
    private array $functions = [];
    private readonly LinesOfCode $linesOfCode;
    private ?int $numClasses         = null;
    private int $numTestedClasses    = 0;
    private ?int $numTraits          = null;
    private int $numTestedTraits     = 0;
    private ?int $numMethods         = null;
    private ?int $numTestedMethods   = null;
    private ?int $numTestedFunctions = null;

    /**
     * @var array<int, array|array{0: Class_, 1: string}|array{0: Function_|ProcessedFunctionType|ProcessedMethodType}|array{0: Trait_, 1: string}>
     */
    private array $codeUnitsByLine = [];

    /**
     * @param non-empty-string                    $sha1
     * @param array<int, ?list<non-empty-string>> $lineCoverageData
     * @param array<string, TestType>             $testData
     * @param array<string, Class_>               $classes
     * @param array<string, Trait_>               $traits
     * @param array<string, Function_>            $functions
     */
    public function __construct(string $name, AbstractNode $parent, string $sha1, array $lineCoverageData, array $functionCoverageData, array $testData, array $classes, array $traits, array $functions, LinesOfCode $linesOfCode)
    {
        parent::__construct($name, $parent);

        $this->sha1                 = $sha1;
        $this->lineCoverageData     = $lineCoverageData;
        $this->functionCoverageData = $functionCoverageData;
        $this->testData             = $testData;
        $this->linesOfCode          = $linesOfCode;

        $this->calculateStatistics($classes, $traits, $functions);
    }

    public function count(): int
    {
        return 1;
    }

    /**
     * @return non-empty-string
     */
    public function sha1(): string
    {
        return $this->sha1;
    }

    /**
     * @return array<int, ?list<non-empty-string>>
     */
    public function lineCoverageData(): array
    {
        return $this->lineCoverageData;
    }

    public function functionCoverageData(): array
    {
        return $this->functionCoverageData;
    }

    /**
     * @return array<string, TestType>
     */
    public function testData(): array
    {
        return $this->testData;
    }

    /**
     * @return array<string, ProcessedClassType>
     */
    public function classes(): array
    {
        return $this->classes;
    }

    /**
     * @return array<string, ProcessedTraitType>
     */
    public function traits(): array
    {
        return $this->traits;
    }

    /**
     * @return array<string, ProcessedFunctionType>
     */
    public function functions(): array
    {
        return $this->functions;
    }

    public function linesOfCode(): LinesOfCode
    {
        return $this->linesOfCode;
    }

    public function numberOfExecutableLines(): int
    {
        return $this->numExecutableLines;
    }

    public function numberOfExecutedLines(): int
    {
        return $this->numExecutedLines;
    }

    public function numberOfExecutableBranches(): int
    {
        return $this->numExecutableBranches;
    }

    public function numberOfExecutedBranches(): int
    {
        return $this->numExecutedBranches;
    }

    public function numberOfExecutablePaths(): int
    {
        return $this->numExecutablePaths;
    }

    public function numberOfExecutedPaths(): int
    {
        return $this->numExecutedPaths;
    }

    public function numberOfClasses(): int
    {
        if ($this->numClasses === null) {
            $this->numClasses = 0;

            foreach ($this->classes as $class) {
                foreach ($class->methods as $method) {
                    if ($method->executableLines > 0) {
                        $this->numClasses++;

                        continue 2;
                    }
                }
            }
        }

        return $this->numClasses;
    }

    public function numberOfTestedClasses(): int
    {
        return $this->numTestedClasses;
    }

    public function numberOfTraits(): int
    {
        if ($this->numTraits === null) {
            $this->numTraits = 0;

            foreach ($this->traits as $trait) {
                foreach ($trait->methods as $method) {
                    if ($method->executableLines > 0) {
                        $this->numTraits++;

                        continue 2;
                    }
                }
            }
        }

        return $this->numTraits;
    }

    public function numberOfTestedTraits(): int
    {
        return $this->numTestedTraits;
    }

    public function numberOfMethods(): int
    {
        if ($this->numMethods === null) {
            $this->numMethods = 0;

            foreach ($this->classes as $class) {
                foreach ($class->methods as $method) {
                    if ($method->executableLines > 0) {
                        $this->numMethods++;
                    }
                }
            }

            foreach ($this->traits as $trait) {
                foreach ($trait->methods as $method) {
                    if ($method->executableLines > 0) {
                        $this->numMethods++;
                    }
                }
            }
        }

        return $this->numMethods;
    }

    public function numberOfTestedMethods(): int
    {
        if ($this->numTestedMethods === null) {
            $this->numTestedMethods = 0;

            foreach ($this->classes as $class) {
                foreach ($class->methods as $method) {
                    if ($method->executableLines > 0 &&
                        $method->coverage === 100) {
                        $this->numTestedMethods++;
                    }
                }
            }

            foreach ($this->traits as $trait) {
                foreach ($trait->methods as $method) {
                    if ($method->executableLines > 0 &&
                        $method->coverage === 100) {
                        $this->numTestedMethods++;
                    }
                }
            }
        }

        return $this->numTestedMethods;
    }

    public function numberOfFunctions(): int
    {
        return count($this->functions);
    }

    public function numberOfTestedFunctions(): int
    {
        if ($this->numTestedFunctions === null) {
            $this->numTestedFunctions = 0;

            foreach ($this->functions as $function) {
                if ($function->executableLines > 0 &&
                    $function->coverage === 100) {
                    $this->numTestedFunctions++;
                }
            }
        }

        return $this->numTestedFunctions;
    }

    /**
     * @param array<string, Class_>    $classes
     * @param array<string, Trait_>    $traits
     * @param array<string, Function_> $functions
     */
    private function calculateStatistics(array $classes, array $traits, array $functions): void
    {
        foreach (range(1, $this->linesOfCode->linesOfCode()) as $lineNumber) {
            $this->codeUnitsByLine[$lineNumber] = [];
        }

        $this->processClasses($classes);
        $this->processTraits($traits);
        $this->processFunctions($functions);

        foreach (range(1, $this->linesOfCode->linesOfCode()) as $lineNumber) {
            if (isset($this->lineCoverageData[$lineNumber])) {
                foreach ($this->codeUnitsByLine[$lineNumber] as &$codeUnit) {
                    $codeUnit->executableLines++;
                }

                unset($codeUnit);

                $this->numExecutableLines++;

                if (count($this->lineCoverageData[$lineNumber]) > 0) {
                    foreach ($this->codeUnitsByLine[$lineNumber] as &$codeUnit) {
                        $codeUnit->executedLines++;
                    }

                    unset($codeUnit);

                    $this->numExecutedLines++;
                }
            }
        }

        foreach ($this->traits as &$trait) {
            foreach ($trait->methods as &$method) {
                $methodLineCoverage   = $method->executableLines > 0 ? ($method->executedLines / $method->executableLines) * 100 : 100;
                $methodBranchCoverage = $method->executableBranches > 0 ? ($method->executedBranches / $method->executableBranches) * 100 : 0;
                $methodPathCoverage   = $method->executablePaths > 0 ? ($method->executedPaths / $method->executablePaths) * 100 : 0;

                $method->coverage = $methodBranchCoverage > 0 ? $methodBranchCoverage : $methodLineCoverage;
                $method->crap     = (new CrapIndex($method->ccn, $methodPathCoverage > 0 ? $methodPathCoverage : $methodLineCoverage))->asString();

                $trait->ccn += $method->ccn;
            }

            unset($method);

            $traitBranchCoverage = $trait->executableBranches > 0 ? ($trait->executedBranches / $trait->executableBranches) * 100 : 0;
            $traitLineCoverage   = $trait->executableLines > 0 ? ($trait->executedLines / $trait->executableLines) * 100 : 100;
            $traitPathCoverage   = $trait->executablePaths > 0 ? ($trait->executedPaths / $trait->executablePaths) * 100 : 0;

            $trait->coverage = $traitBranchCoverage > 0 ? $traitBranchCoverage : $traitLineCoverage;
            $trait->crap     = (new CrapIndex($trait->ccn, $traitPathCoverage > 0 ? $traitPathCoverage : $traitLineCoverage))->asString();

            if ($trait->executableLines > 0 && $trait->coverage === 100) {
                $this->numTestedTraits++;
            }
        }

        unset($trait);

        foreach ($this->classes as &$class) {
            foreach ($class->methods as &$method) {
                $methodLineCoverage   = $method->executableLines > 0 ? ($method->executedLines / $method->executableLines) * 100 : 100;
                $methodBranchCoverage = $method->executableBranches > 0 ? ($method->executedBranches / $method->executableBranches) * 100 : 0;
                $methodPathCoverage   = $method->executablePaths > 0 ? ($method->executedPaths / $method->executablePaths) * 100 : 0;

                $method->coverage = $methodBranchCoverage > 0 ? $methodBranchCoverage : $methodLineCoverage;
                $method->crap     = (new CrapIndex($method->ccn, $methodPathCoverage > 0 ? $methodPathCoverage : $methodLineCoverage))->asString();

                $class->ccn += $method->ccn;
            }

            unset($method);

            $classLineCoverage   = $class->executableLines > 0 ? ($class->executedLines / $class->executableLines) * 100 : 100;
            $classBranchCoverage = $class->executableBranches > 0 ? ($class->executedBranches / $class->executableBranches) * 100 : 0;
            $classPathCoverage   = $class->executablePaths > 0 ? ($class->executedPaths / $class->executablePaths) * 100 : 0;

            $class->coverage = $classBranchCoverage > 0 ? $classBranchCoverage : $classLineCoverage;
            $class->crap     = (new CrapIndex($class->ccn, $classPathCoverage > 0 ? $classPathCoverage : $classLineCoverage))->asString();

            if ($class->executableLines > 0 && $class->coverage === 100) {
                $this->numTestedClasses++;
            }
        }

        unset($class);

        foreach ($this->functions as &$function) {
            $functionLineCoverage   = $function->executableLines > 0 ? ($function->executedLines / $function->executableLines) * 100 : 100;
            $functionBranchCoverage = $function->executableBranches > 0 ? ($function->executedBranches / $function->executableBranches) * 100 : 0;
            $functionPathCoverage   = $function->executablePaths > 0 ? ($function->executedPaths / $function->executablePaths) * 100 : 0;

            $function->coverage = $functionBranchCoverage > 0 ? $functionBranchCoverage : $functionLineCoverage;
            $function->crap     = (new CrapIndex($function->ccn, $functionPathCoverage > 0 ? $functionPathCoverage : $functionLineCoverage))->asString();

            if ($function->coverage === 100) {
                $this->numTestedFunctions++;
            }
        }
    }

    /**
     * @param array<string, Class_> $classes
     */
    private function processClasses(array $classes): void
    {
        $link = $this->id() . '.html#';

        foreach ($classes as $className => $class) {
            $this->classes[$className] = new ProcessedClassType(
                $className,
                $class->namespace(),
                [],
                $class->startLine(),
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                $link . $class->startLine(),
            );

            foreach ($class->methods() as $methodName => $method) {
                $methodData                                      = $this->newMethod($className, $method, $link);
                $this->classes[$className]->methods[$methodName] = $methodData;

                $this->classes[$className]->executableBranches += $methodData->executableBranches;
                $this->classes[$className]->executedBranches   += $methodData->executedBranches;
                $this->classes[$className]->executablePaths    += $methodData->executablePaths;
                $this->classes[$className]->executedPaths      += $methodData->executedPaths;

                $this->numExecutableBranches += $methodData->executableBranches;
                $this->numExecutedBranches   += $methodData->executedBranches;
                $this->numExecutablePaths    += $methodData->executablePaths;
                $this->numExecutedPaths      += $methodData->executedPaths;

                foreach (range($method->startLine(), $method->endLine()) as $lineNumber) {
                    $this->codeUnitsByLine[$lineNumber] = [
                        &$this->classes[$className],
                        &$this->classes[$className]->methods[$methodName],
                    ];
                }
            }
        }
    }

    /**
     * @param array<string, Trait_> $traits
     */
    private function processTraits(array $traits): void
    {
        $link = $this->id() . '.html#';

        foreach ($traits as $traitName => $trait) {
            $this->traits[$traitName] = new ProcessedTraitType(
                $traitName,
                $trait->namespace(),
                [],
                $trait->startLine(),
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                0,
                $link . $trait->startLine(),
            );

            foreach ($trait->methods() as $methodName => $method) {
                $methodData                                     = $this->newMethod($traitName, $method, $link);
                $this->traits[$traitName]->methods[$methodName] = $methodData;

                $this->traits[$traitName]->executableBranches += $methodData->executableBranches;
                $this->traits[$traitName]->executedBranches   += $methodData->executedBranches;
                $this->traits[$traitName]->executablePaths    += $methodData->executablePaths;
                $this->traits[$traitName]->executedPaths      += $methodData->executedPaths;

                $this->numExecutableBranches += $methodData->executableBranches;
                $this->numExecutedBranches   += $methodData->executedBranches;
                $this->numExecutablePaths    += $methodData->executablePaths;
                $this->numExecutedPaths      += $methodData->executedPaths;

                foreach (range($method->startLine(), $method->endLine()) as $lineNumber) {
                    $this->codeUnitsByLine[$lineNumber] = [
                        &$this->traits[$traitName],
                        &$this->traits[$traitName]->methods[$methodName],
                    ];
                }
            }
        }
    }

    /**
     * @param array<string, Function_> $functions
     */
    private function processFunctions(array $functions): void
    {
        $link = $this->id() . '.html#';

        foreach ($functions as $functionName => $function) {
            $this->functions[$functionName] = new ProcessedFunctionType(
                $functionName,
                $function->namespace(),
                $function->signature(),
                $function->startLine(),
                $function->endLine(),
                0,
                0,
                0,
                0,
                0,
                0,
                $function->cyclomaticComplexity(),
                0,
                0,
                $link . $function->startLine(),
            );

            foreach (range($function->startLine(), $function->endLine()) as $lineNumber) {
                $this->codeUnitsByLine[$lineNumber] = [&$this->functions[$functionName]];
            }

            if (isset($this->functionCoverageData[$functionName])) {
                $this->functions[$functionName]->executableBranches = count(
                    $this->functionCoverageData[$functionName]->branches,
                );

                $this->functions[$functionName]->executedBranches = count(
                    array_filter(
                        $this->functionCoverageData[$functionName]->branches,
                        static function (ProcessedBranchCoverageData $branch)
                        {
                            return (bool) $branch->hit;
                        },
                    ),
                );
            }

            if (isset($this->functionCoverageData[$functionName])) {
                $this->functions[$functionName]->executablePaths = count(
                    $this->functionCoverageData[$functionName]->paths,
                );

                $this->functions[$functionName]->executedPaths = count(
                    array_filter(
                        $this->functionCoverageData[$functionName]->paths,
                        static function (ProcessedPathCoverageData $path)
                        {
                            return (bool) $path->hit;
                        },
                    ),
                );
            }

            $this->numExecutableBranches += $this->functions[$functionName]->executableBranches;
            $this->numExecutedBranches   += $this->functions[$functionName]->executedBranches;
            $this->numExecutablePaths    += $this->functions[$functionName]->executablePaths;
            $this->numExecutedPaths      += $this->functions[$functionName]->executedPaths;
        }
    }

    private function newMethod(string $className, Method $method, string $link): ProcessedMethodType
    {
        $key = $className . '->' . $method->name();

        $executableBranches = 0;
        $executedBranches   = 0;

        if (isset($this->functionCoverageData[$key])) {
            $executableBranches = count(
                $this->functionCoverageData[$key]->branches,
            );

            $executedBranches = count(
                array_filter(
                    $this->functionCoverageData[$key]->branches,
                    static function (ProcessedBranchCoverageData $branch)
                    {
                        return (bool) $branch->hit;
                    },
                ),
            );
        }

        $executablePaths = 0;
        $executedPaths   = 0;

        if (isset($this->functionCoverageData[$key])) {
            $executablePaths = count(
                $this->functionCoverageData[$key]->paths,
            );

            $executedPaths = count(
                array_filter(
                    $this->functionCoverageData[$key]->paths,
                    static function (ProcessedPathCoverageData $path)
                    {
                        return (bool) $path->hit;
                    },
                ),
            );
        }

        return new ProcessedMethodType(
            $method->name(),
            $method->visibility()->value,
            $method->signature(),
            $method->startLine(),
            $method->endLine(),
            0,
            0,
            $executableBranches,
            $executedBranches,
            $executablePaths,
            $executedPaths,
            $method->cyclomaticComplexity(),
            0,
            0,
            $link . $method->startLine(),
        );
    }
}
