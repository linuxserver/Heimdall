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
abstract class Node
{
    protected readonly XMLWriter $xmlWriter;

    public function __construct(XMLWriter $xmlWriter)
    {
        $this->xmlWriter = $xmlWriter;
    }

    public function totals(): Totals
    {
        return new Totals($this->xmlWriter);
    }

    public function addDirectory(): Directory
    {
        return new Directory($this->xmlWriter);
    }

    public function addFile(): File
    {
        return new File($this->xmlWriter);
    }

    public function getWriter(): XMLWriter
    {
        return $this->xmlWriter;
    }
}
