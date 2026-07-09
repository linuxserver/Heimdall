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
final readonly class Method
{
    private XMLWriter $xmlWriter;

    public function __construct(
        XMLWriter $xmlWriter,
        string $name,
        string $signature,
        string $start,
        ?string $end,
        string $executable,
        string $executed,
        string $coverage,
        string $crap
    ) {
        $this->xmlWriter = $xmlWriter;

        $this->xmlWriter->writeAttribute('name', $name);
        $this->xmlWriter->writeAttribute('signature', $signature);

        $this->xmlWriter->writeAttribute('start', $start);

        if ($end !== null) {
            $this->xmlWriter->writeAttribute('end', $end);
        }

        $this->xmlWriter->writeAttribute('crap', $crap);

        $this->xmlWriter->writeAttribute('executable', $executable);
        $this->xmlWriter->writeAttribute('executed', $executed);
        $this->xmlWriter->writeAttribute('coverage', $coverage);
    }
}
