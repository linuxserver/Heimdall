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

use function sprintf;
use SebastianBergmann\CodeCoverage\CodeCoverage;
use XMLWriter;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 *
 * @phpstan-import-type TestType from CodeCoverage
 */
final readonly class Tests
{
    private readonly XMLWriter $xmlWriter;

    public function __construct(XMLWriter $xmlWriter)
    {
        $this->xmlWriter = $xmlWriter;
    }

    /**
     * @param TestType $result
     */
    public function addTest(string $test, array $result): void
    {
        $this->xmlWriter->startElement('test');

        $this->xmlWriter->writeAttribute('name', $test);
        $this->xmlWriter->writeAttribute('size', $result['size']);
        $this->xmlWriter->writeAttribute('status', $result['status']);
        $this->xmlWriter->writeAttribute('time', sprintf('%F', $result['time']));

        $this->xmlWriter->endElement();
    }
}
