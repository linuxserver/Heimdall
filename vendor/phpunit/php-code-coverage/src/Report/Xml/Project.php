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

use DateTimeImmutable;
use SebastianBergmann\Environment\Runtime;
use XMLWriter;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Project extends Node
{
    private readonly string $directory;

    public function __construct(XMLWriter $xmlWriter, string $directory)
    {
        $this->directory = $directory;

        parent::__construct($xmlWriter);

        $this->xmlWriter->startDocument();

        $this->xmlWriter->startElement('phpunit');
        $this->xmlWriter->writeAttribute('xmlns', Facade::XML_NAMESPACE);
    }

    public function projectSourceDirectory(): string
    {
        return $this->directory;
    }

    public function buildInformation(
        Runtime $runtime,
        DateTimeImmutable $buildDate,
        string $phpUnitVersion,
        string $coverageVersion,
        string $driverExtensionName,
        string $driverExtensionVersion,
    ): void {
        new BuildInformation(
            $this->xmlWriter,
            $runtime,
            $buildDate,
            $phpUnitVersion,
            $coverageVersion,
            $driverExtensionName,
            $driverExtensionVersion,
        );
    }

    public function tests(): Tests
    {
        return new Tests($this->xmlWriter);
    }

    public function getWriter(): XMLWriter
    {
        return $this->xmlWriter;
    }

    public function startProject(): void
    {
        $this->xmlWriter->startElement('project');
        $this->xmlWriter->writeAttribute('source', $this->directory);
    }

    public function finalize(): void
    {
        $this->xmlWriter->endElement();
        $this->xmlWriter->endDocument();
        $this->xmlWriter->flush();
    }
}
