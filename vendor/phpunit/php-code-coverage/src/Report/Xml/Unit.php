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

use XMLWriter;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final readonly class Unit
{
    private XMLWriter $xmlWriter;

    public function __construct(
        XMLWriter $xmlWriter,
        string $name,
        string $namespace,
        int $start,
        int $executable,
        int $executed,
        float $crap
    ) {
        $this->xmlWriter = $xmlWriter;

        $this->xmlWriter->writeAttribute('name', $name);
        $this->xmlWriter->writeAttribute('start', (string) $start);
        $this->xmlWriter->writeAttribute('executable', (string) $executable);
        $this->xmlWriter->writeAttribute('executed', (string) $executed);
        $this->xmlWriter->writeAttribute('crap', (string) $crap);

        $this->xmlWriter->startElement('namespace');
        $this->xmlWriter->writeAttribute('name', $namespace);
        $this->xmlWriter->endElement();
    }

    public function addMethod(
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
}
