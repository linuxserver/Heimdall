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
class File
{
    protected XMLWriter $xmlWriter;

    public function __construct(XMLWriter $xmlWriter)
    {
        $this->xmlWriter = $xmlWriter;
    }

    public function getWriter(): XMLWriter
    {
        return $this->xmlWriter;
    }

    public function totals(): Totals
    {
        return new Totals($this->xmlWriter);
    }

    public function lineCoverage(string $line): Coverage
    {
        return new Coverage($this->xmlWriter, $line);
    }
}
