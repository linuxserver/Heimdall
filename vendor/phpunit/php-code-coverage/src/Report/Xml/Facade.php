<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Report\Xml;

use const DIRECTORY_SEPARATOR;
use function count;
use function dirname;
use function file_get_contents;
use function is_array;
use function is_dir;
use function is_file;
use function is_writable;
use function phpversion;
use function sprintf;
use function strlen;
use function substr;
use DateTimeImmutable;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Data\ProcessedClassType;
use SebastianBergmann\CodeCoverage\Data\ProcessedFunctionType;
use SebastianBergmann\CodeCoverage\Data\ProcessedTraitType;
use SebastianBergmann\CodeCoverage\Node\AbstractNode;
use SebastianBergmann\CodeCoverage\Node\Directory as DirectoryNode;
use SebastianBergmann\CodeCoverage\Node\File as FileNode;
use SebastianBergmann\CodeCoverage\PathExistsButIsNotDirectoryException;
use SebastianBergmann\CodeCoverage\Util\Filesystem;
use SebastianBergmann\CodeCoverage\Version;
use SebastianBergmann\CodeCoverage\WriteOperationFailedException;
use SebastianBergmann\CodeCoverage\XmlException;
use SebastianBergmann\Environment\Runtime;
use XMLWriter;

/**
 * @phpstan-import-type TestType from CodeCoverage
 */
final class Facade
{
    public const string XML_NAMESPACE = 'https://schema.phpunit.de/coverage/1.0';
    private string $target;
    private Project $project;
    private readonly string $phpUnitVersion;
    private readonly bool $includeSource;

    public function __construct(string $version, bool $includeSource = true)
    {
        $this->phpUnitVersion = $version;
        $this->includeSource  = $includeSource;
    }

    /**
     * @throws XmlException
     */
    public function process(CodeCoverage $coverage, string $target): void
    {
        if (substr($target, -1, 1) !== DIRECTORY_SEPARATOR) {
            $target .= DIRECTORY_SEPARATOR;
        }

        $this->target = $target;
        $this->initTargetDirectory($target);

        $report = $coverage->getReport();

        $writer = new XMLWriter;
        $writer->openUri($this->targetFilePath('index'));
        $writer->setIndent(true);
        $writer->setIndentString('  ');
        $this->project = new Project(
            $writer,
            $coverage->getReport()->name(),
        );

        $this->setBuildInformation($coverage);

        $this->project->startProject();
        $this->processTests($coverage->getTests());
        $this->processDirectory($report, $this->project);
        $this->project->finalize();
    }

    private function setBuildInformation(CodeCoverage $coverage): void
    {
        if ($coverage->driverIsPcov()) {
            $driverExtensionName    = 'pcov';
            $driverExtensionVersion = phpversion('pcov');
        } elseif ($coverage->driverIsXdebug()) {
            $driverExtensionName    = 'xdebug';
            $driverExtensionVersion = phpversion('xdebug');
        } else {
            // @codeCoverageIgnoreStart
            $driverExtensionName    = 'unknown';
            $driverExtensionVersion = 'unknown';
            // @codeCoverageIgnoreEnd
        }

        $this->project->buildInformation(
            new Runtime,
            new DateTimeImmutable,
            $this->phpUnitVersion,
            Version::id(),
            $driverExtensionName,
            $driverExtensionVersion,
        );
    }

    /**
     * @throws PathExistsButIsNotDirectoryException
     * @throws WriteOperationFailedException
     */
    private function initTargetDirectory(string $directory): void
    {
        if (is_file($directory)) {
            // @codeCoverageIgnoreStart
            if (!is_dir($directory)) {
                throw new PathExistsButIsNotDirectoryException($directory);
            }

            if (!is_writable($directory)) {
                throw new WriteOperationFailedException($directory);
            }
            // @codeCoverageIgnoreEnd
        }

        Filesystem::createDirectory($directory);
    }

    /**
     * @throws XmlException
     */
    private function processDirectory(DirectoryNode $directory, Node $context): void
    {
        $directoryName = $directory->name();

        if ($this->project->projectSourceDirectory() === $directoryName) {
            $directoryName = '/';
        }

        $writer = $this->project->getWriter();
        $writer->startElement('directory');
        $writer->writeAttribute('name', $directoryName);
        $directoryObject = $context->addDirectory();

        $this->setTotals($directory, $directoryObject->totals());

        foreach ($directory->directories() as $node) {
            $this->processDirectory($node, $directoryObject);
        }

        foreach ($directory->files() as $node) {
            $this->processFile($node, $directoryObject);
        }
        $writer->endElement();
    }

    /**
     * @throws XmlException
     */
    private function processFile(FileNode $file, Directory $context): void
    {
        $context->getWriter()->startElement('file');
        $context->getWriter()->writeAttribute('name', $file->name());
        $context->getWriter()->writeAttribute('href', $file->id() . '.xml');
        $context->getWriter()->writeAttribute('hash', $file->sha1());

        $fileObject = $context->addFile();

        $this->setTotals($file, $fileObject->totals());

        $context->getWriter()->endElement();

        $path = substr(
            $file->pathAsString(),
            strlen($this->project->projectSourceDirectory()),
        );

        $writer = new XMLWriter;
        $writer->openUri($this->targetFilePath($file->id()));
        $writer->setIndent(true);
        $writer->setIndentString('  ');
        $fileReport = new Report($writer, $path, $file->sha1());

        $this->setTotals($file, $fileReport->totals());

        foreach ($file->classesAndTraits() as $unit) {
            $this->processUnit($unit, $fileReport);
        }

        foreach ($file->functions() as $function) {
            $this->processFunction($function, $fileReport);
        }

        $fileReport->getWriter()->startElement('coverage');

        foreach ($file->lineCoverageData() as $line => $tests) {
            if (!is_array($tests) || count($tests) === 0) {
                continue;
            }

            $coverage = $fileReport->lineCoverage((string) $line);
            $coverage->finalize($tests);
        }
        $fileReport->getWriter()->endElement();

        if ($this->includeSource) {
            $fileReport->source()->setSourceCode(
                file_get_contents($file->pathAsString()),
            );
        }

        $fileReport->finalize();
    }

    private function processUnit(ProcessedClassType|ProcessedTraitType $unit, Report $report): void
    {
        if ($unit instanceof ProcessedClassType) {
            $report->getWriter()->startElement('class');

            $unitObject = $report->classObject(
                $unit->className,
                $unit->namespace,
                $unit->startLine,
                $unit->executableLines,
                $unit->executedLines,
                (float) $unit->crap,
            );
        } else {
            $report->getWriter()->startElement('trait');

            $unitObject = $report->traitObject(
                $unit->traitName,
                $unit->namespace,
                $unit->startLine,
                $unit->executableLines,
                $unit->executedLines,
                (float) $unit->crap,
            );
        }

        foreach ($unit->methods as $method) {
            $report->getWriter()->startElement('method');

            $unitObject->addMethod(
                $method->methodName,
                $method->signature,
                (string) $method->startLine,
                (string) $method->endLine,
                (string) $method->executableLines,
                (string) $method->executedLines,
                (string) $method->coverage,
                $method->crap,
            );

            $report->getWriter()->endElement();
        }

        $report->getWriter()->endElement();
    }

    private function processFunction(ProcessedFunctionType $function, Report $report): void
    {
        $report->getWriter()->startElement('function');

        $report->functionObject(
            $function->functionName,
            $function->signature,
            (string) $function->startLine,
            null,
            (string) $function->executableLines,
            (string) $function->executedLines,
            (string) $function->coverage,
            $function->crap,
        );

        $report->getWriter()->endElement();
    }

    /**
     * @param array<string, TestType> $tests
     */
    private function processTests(array $tests): void
    {
        $this->project->getWriter()->startElement('tests');

        $testsObject = $this->project->tests();

        foreach ($tests as $test => $result) {
            $testsObject->addTest($test, $result);
        }

        $this->project->getWriter()->endElement();
    }

    private function setTotals(AbstractNode $node, Totals $totals): void
    {
        $totals->getWriter()->startElement('totals');

        $loc = $node->linesOfCode();

        $totals->setNumLines(
            $loc->linesOfCode(),
            $loc->commentLinesOfCode(),
            $loc->nonCommentLinesOfCode(),
            $node->numberOfExecutableLines(),
            $node->numberOfExecutedLines(),
        );

        $totals->setNumMethods(
            $node->numberOfMethods(),
            $node->numberOfTestedMethods(),
        );

        $totals->setNumFunctions(
            $node->numberOfFunctions(),
            $node->numberOfTestedFunctions(),
        );

        $totals->setNumClasses(
            $node->numberOfClasses(),
            $node->numberOfTestedClasses(),
        );

        $totals->setNumTraits(
            $node->numberOfTraits(),
            $node->numberOfTestedTraits(),
        );

        $totals->getWriter()->endElement();
    }

    private function targetDirectory(): string
    {
        return $this->target;
    }

    private function targetFilePath(string $name): string
    {
        $filename = sprintf('%s/%s.xml', $this->targetDirectory(), $name);

        $this->initTargetDirectory(dirname($filename));

        return $filename;
    }
}
