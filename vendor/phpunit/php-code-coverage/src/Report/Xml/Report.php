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

use function basename;
use function dirname;
use DOMDocument;
use XMLWriter;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Report extends File
{
    private readonly string $name;
    private readonly string $sha1;

    public function __construct(XMLWriter $xmlWriter, string $name, string $sha1)
    {
        /*
        $dom = new DOMDocument;
        $dom->loadXML('<?xml version="1.0" ?><phpunit xmlns="https://schema.phpunit.de/coverage/1.0"><file /></phpunit>');

        $contextNode = $dom->getElementsByTagNameNS(
            Facade::XML_NAMESPACE,
            'file',
        )->item(0);
*/
        parent::__construct($xmlWriter);

        $this->name = $name;
        $this->sha1 = $sha1;

        $xmlWriter->startDocument();
        $xmlWriter->startElement('phpunit');
        $xmlWriter->writeAttribute('xmlns', Facade::XML_NAMESPACE);
        $xmlWriter->startElement('file');
        $xmlWriter->writeAttribute('name', basename($this->name));
        $xmlWriter->writeAttribute('path', dirname($this->name));
        $xmlWriter->writeAttribute('hash', $this->sha1);
    }

    public function finalize(): void
    {
        $this->xmlWriter->endElement();
        $this->xmlWriter->endElement();

        $this->xmlWriter->endDocument();
        $this->xmlWriter->flush();
    }

    public function functionObject(
        string $name,
        string $signature,
        string $start,
        ?string $end,
        string $executable,
        string $executed,
        string $coverage,
        string $crap
    ): void {
        new Method(
            $this->xmlWriter,
            $name,
            $signature,
            $start,
            $end,
            $executable,
            $executed,
            $coverage,
            $crap,
        );
    }

    public function classObject(
        string $name,
        string $namespace,
        int $start,
        int $executable,
        int $executed,
        float $crap
    ): Unit {
        return new Unit($this->xmlWriter, $name, $namespace, $start, $executable, $executed, $crap);
    }

    public function traitObject(
        string $name,
        string $namespace,
        int $start,
        int $executable,
        int $executed,
        float $crap
    ): Unit {
        return new Unit($this->xmlWriter, $name, $namespace, $start, $executable, $executed, $crap);
    }

    public function source(): Source
    {
        return new Source($this->xmlWriter);
    }
}
