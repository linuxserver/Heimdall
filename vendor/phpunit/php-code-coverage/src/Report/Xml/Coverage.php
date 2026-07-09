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
final class Coverage
{
    private readonly XMLWriter $xmlWriter;
    private readonly string $line;

    public function __construct(
        XMLWriter $xmlWriter,
        string $line
    ) {
        $this->xmlWriter = $xmlWriter;
        $this->line      = $line;
    }

    public function finalize(array $tests): void
    {
        $writer = $this->xmlWriter;
        $writer->startElement('line');
        $writer->writeAttribute('nr', $this->line);

        foreach ($tests as $test) {
            $writer->startElement('covered');
            $writer->writeAttribute('by', $test);
            $writer->endElement();
        }
        $writer->endElement();
    }
}
