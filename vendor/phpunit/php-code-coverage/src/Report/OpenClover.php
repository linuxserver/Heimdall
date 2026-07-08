<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Report;

use function assert;
use function basename;
use function count;
use function is_string;
use function ksort;
use function max;
use function range;
use function str_replace;
use function time;
use DOMDocument;
use DOMElement;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Node\File;
use SebastianBergmann\CodeCoverage\Util\Filesystem;
use SebastianBergmann\CodeCoverage\Util\Xml;
use SebastianBergmann\CodeCoverage\Version;
use SebastianBergmann\CodeCoverage\WriteOperationFailedException;

final class OpenClover
{
    /**
     * @throws WriteOperationFailedException
     */
    public function process(CodeCoverage $coverage, ?string $target = null, ?string $name = null): string
    {
        $time = (string) time();

        $xmlDocument               = new DOMDocument('1.0', 'UTF-8');
        $xmlDocument->formatOutput = true;

        $xmlCoverage = $xmlDocument->createElement('coverage');
        $xmlCoverage->setAttribute('clover', Version::id());
        $xmlCoverage->setAttribute('generated', $time);
        $xmlDocument->appendChild($xmlCoverage);

        $xmlProject = $xmlDocument->createElement('project');
        $xmlProject->setAttribute('timestamp', $time);

        if (is_string($name)) {
            $xmlProject->setAttribute('name', $name);
        }

        $xmlCoverage->appendChild($xmlProject);

        /** @var array<non-empty-string, DOMElement> $packages */
        $packages = [];
        $report   = $coverage->getReport();

        foreach ($report as $item) {
            if (!$item instanceof File) {
                continue;
            }

            $xmlFile = $xmlDocument->createElement('file');
            $xmlFile->setAttribute('name', basename($item->pathAsString()));
            $xmlFile->setAttribute('path', $item->pathAsString());

            $classes      = $item->classesAndTraits();
            $coverageData = $item->lineCoverageData();
            $lines        = [];
            $namespace    = 'global';

            foreach ($classes as $className => $class) {
                $classStatements        = 0;
                $coveredClassStatements = 0;
                $coveredMethods         = 0;
                $classMethods           = 0;

                // Assumption: one namespace per file
                if ($class->namespace !== '') {
                    $namespace = $class->namespace;
                }

                foreach ($class->methods as $methodName => $method) {
                    /** @phpstan-ignore equal.notAllowed */
                    if ($method->executableLines == 0) {
                        continue;
                    }

                    $classMethods++;
                    $classStatements        += $method->executableLines;
                    $coveredClassStatements += $method->executedLines;

                    /** @phpstan-ignore equal.notAllowed */
                    if ($method->coverage == 100) {
                        $coveredMethods++;
                    }

                    $methodCount = 0;

                    foreach (range($method->startLine, $method->endLine) as $line) {
                        if (isset($coverageData[$line])) {
                            $methodCount = max($methodCount, count($coverageData[$line]));
                        }
                    }

                    $lines[$method->startLine] = [
                        'ccn'        => $method->ccn,
                        'count'      => $methodCount,
                        'type'       => 'method',
                        'signature'  => $method->signature,
                        'visibility' => $method->visibility,
                    ];
                }

                $xmlClass = $xmlDocument->createElement('class');
                $xmlClass->setAttribute('name', str_replace($class->namespace . '\\', '', $className));

                $xmlFile->appendChild($xmlClass);

                $xmlMetrics = $xmlDocument->createElement('metrics');
                $xmlMetrics->setAttribute('complexity', (string) $class->ccn);
                $xmlMetrics->setAttribute('elements', (string) ($classMethods + $classStatements + $class->executableBranches));
                $xmlMetrics->setAttribute('coveredelements', (string) ($coveredMethods + $coveredClassStatements + $class->executedBranches));
                $xmlMetrics->setAttribute('conditionals', (string) $class->executableBranches);
                $xmlMetrics->setAttribute('coveredconditionals', (string) $class->executedBranches);
                $xmlMetrics->setAttribute('statements', (string) $classStatements);
                $xmlMetrics->setAttribute('coveredstatements', (string) $coveredClassStatements);
                $xmlMetrics->setAttribute('methods', (string) $classMethods);
                $xmlMetrics->setAttribute('coveredmethods', (string) $coveredMethods);
                $xmlClass->insertBefore($xmlMetrics, $xmlClass->firstChild);
            }

            foreach ($coverageData as $line => $data) {
                if ($data === null || isset($lines[$line])) {
                    continue;
                }

                $lines[$line] = [
                    'count' => count($data),
                    'type'  => 'stmt',
                ];
            }

            ksort($lines);

            foreach ($lines as $line => $data) {
                $xmlLine = $xmlDocument->createElement('line');
                $xmlLine->setAttribute('num', (string) $line);
                $xmlLine->setAttribute('type', $data['type']);

                if (isset($data['ccn'])) {
                    $xmlLine->setAttribute('complexity', (string) $data['ccn']);
                }

                $xmlLine->setAttribute('count', (string) $data['count']);

                if (isset($data['signature'])) {
                    $xmlLine->setAttribute('signature', $data['signature']);
                }

                if (isset($data['visibility'])) {
                    $xmlLine->setAttribute('visibility', $data['visibility']);
                }

                $xmlFile->appendChild($xmlLine);
            }

            $linesOfCode = $item->linesOfCode();

            $xmlMetrics = $xmlDocument->createElement('metrics');
            $xmlMetrics->setAttribute('loc', (string) $linesOfCode->linesOfCode());
            $xmlMetrics->setAttribute('ncloc', (string) $linesOfCode->nonCommentLinesOfCode());
            $xmlMetrics->setAttribute('classes', (string) $item->numberOfClassesAndTraits());
            $xmlMetrics->setAttribute('complexity', (string) $item->cyclomaticComplexity());
            $xmlMetrics->setAttribute('elements', (string) ($item->numberOfMethods() + $item->numberOfExecutableLines() + $item->numberOfExecutableBranches()));
            $xmlMetrics->setAttribute('coveredelements', (string) ($item->numberOfTestedMethods() + $item->numberOfExecutedLines() + $item->numberOfExecutedBranches()));
            $xmlMetrics->setAttribute('conditionals', (string) $item->numberOfExecutableBranches());
            $xmlMetrics->setAttribute('coveredconditionals', (string) $item->numberOfExecutedBranches());
            $xmlMetrics->setAttribute('statements', (string) $item->numberOfExecutableLines());
            $xmlMetrics->setAttribute('coveredstatements', (string) $item->numberOfExecutedLines());
            $xmlMetrics->setAttribute('methods', (string) $item->numberOfMethods());
            $xmlMetrics->setAttribute('coveredmethods', (string) $item->numberOfTestedMethods());
            $xmlFile->insertBefore($xmlMetrics, $xmlFile->firstChild);

            if (!isset($packages[$namespace])) {
                $packages[$namespace] = $xmlDocument->createElement('package');
                $packages[$namespace]->setAttribute('name', $namespace);

                $xmlPackageMetrics = $xmlDocument->createElement('metrics');
                $xmlPackageMetrics->setAttribute('complexity', '0');
                $xmlPackageMetrics->setAttribute('elements', '0');
                $xmlPackageMetrics->setAttribute('coveredelements', '0');
                $xmlPackageMetrics->setAttribute('conditionals', '0');
                $xmlPackageMetrics->setAttribute('coveredconditionals', '0');
                $xmlPackageMetrics->setAttribute('statements', '0');
                $xmlPackageMetrics->setAttribute('coveredstatements', '0');
                $xmlPackageMetrics->setAttribute('methods', '0');
                $xmlPackageMetrics->setAttribute('coveredmethods', '0');
                $packages[$namespace]->appendChild($xmlPackageMetrics);

                $xmlProject->appendChild($packages[$namespace]);
            }

            $xmlPackageMetrics = $packages[$namespace]->firstChild;

            assert($xmlPackageMetrics instanceof DOMElement);

            $xmlPackageMetrics->setAttribute('complexity', (string) ((int) $xmlPackageMetrics->getAttribute('complexity') + $item->cyclomaticComplexity()));
            $xmlPackageMetrics->setAttribute('elements', (string) ((int) $xmlPackageMetrics->getAttribute('elements') + $item->numberOfMethods() + $item->numberOfExecutableLines() + $item->numberOfExecutableBranches()));
            $xmlPackageMetrics->setAttribute('coveredelements', (string) ((int) $xmlPackageMetrics->getAttribute('coveredelements') + $item->numberOfTestedMethods() + $item->numberOfExecutedLines() + $item->numberOfExecutedBranches()));
            $xmlPackageMetrics->setAttribute('conditionals', (string) ((int) $xmlPackageMetrics->getAttribute('conditionals') + $item->numberOfExecutableBranches()));
            $xmlPackageMetrics->setAttribute('coveredconditionals', (string) ((int) $xmlPackageMetrics->getAttribute('coveredconditionals') + $item->numberOfExecutedBranches()));
            $xmlPackageMetrics->setAttribute('statements', (string) ((int) $xmlPackageMetrics->getAttribute('statements') + $item->numberOfExecutableLines()));
            $xmlPackageMetrics->setAttribute('coveredstatements', (string) ((int) $xmlPackageMetrics->getAttribute('coveredstatements') + $item->numberOfExecutedLines()));
            $xmlPackageMetrics->setAttribute('methods', (string) ((int) $xmlPackageMetrics->getAttribute('methods') + $item->numberOfMethods()));
            $xmlPackageMetrics->setAttribute('coveredmethods', (string) ((int) $xmlPackageMetrics->getAttribute('coveredmethods') + $item->numberOfTestedMethods()));

            $packages[$namespace]->appendChild($xmlFile);
        }

        $linesOfCode = $report->linesOfCode();

        $xmlMetrics = $xmlDocument->createElement('metrics');
        $xmlMetrics->setAttribute('files', (string) count($report));
        $xmlMetrics->setAttribute('loc', (string) $linesOfCode->linesOfCode());
        $xmlMetrics->setAttribute('ncloc', (string) $linesOfCode->nonCommentLinesOfCode());
        $xmlMetrics->setAttribute('classes', (string) $report->numberOfClassesAndTraits());
        $xmlMetrics->setAttribute('complexity', (string) $report->cyclomaticComplexity());
        $xmlMetrics->setAttribute('elements', (string) ($report->numberOfMethods() + $report->numberOfExecutableLines() + $report->numberOfExecutableBranches()));
        $xmlMetrics->setAttribute('coveredelements', (string) ($report->numberOfTestedMethods() + $report->numberOfExecutedLines() + $report->numberOfExecutedBranches()));
        $xmlMetrics->setAttribute('conditionals', (string) $report->numberOfExecutableBranches());
        $xmlMetrics->setAttribute('coveredconditionals', (string) $report->numberOfExecutedBranches());
        $xmlMetrics->setAttribute('statements', (string) $report->numberOfExecutableLines());
        $xmlMetrics->setAttribute('coveredstatements', (string) $report->numberOfExecutedLines());
        $xmlMetrics->setAttribute('methods', (string) $report->numberOfMethods());
        $xmlMetrics->setAttribute('coveredmethods', (string) $report->numberOfTestedMethods());
        $xmlProject->insertBefore($xmlMetrics, $xmlProject->firstChild);

        $buffer = Xml::asString($xmlDocument);

        if ($target !== null) {
            Filesystem::write($target, $buffer);
        }

        return $buffer;
    }
}
