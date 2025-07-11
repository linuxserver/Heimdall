<?php
/**
 * Tests for the \PHP_CodeSniffer\Config --report, --report-file and --report-* arguments.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Config;

use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHP_CodeSniffer\Config --report, --report-file and --report-* arguments.
 *
 * @covers \PHP_CodeSniffer\Config::processLongArgument
 */
final class ReportArgsTest extends TestCase
{


    /**
     * [CS mode] Verify that passing `--report-file` does not influence *which* reports get activated.
     *
     * @return void
     */
    public function testReportFileDoesNotSetReportsCs()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $config = new ConfigDouble(['--report-file='.__DIR__.'/report.txt']);

        $this->assertTrue(is_string($config->reportFile));
        $this->assertStringEndsWith('/report.txt', $config->reportFile);
        $this->assertSame(['full' => null], $config->reports);

    }//end testReportFileDoesNotSetReportsCs()


    /**
     * [CBF mode] Verify that passing `--report-file` does not influence *which* reports get activated.
     *
     * @group CBF
     *
     * @return void
     */
    public function testReportFileDoesNotSetReportsCbf()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $config = new ConfigDouble(['--report-file='.__DIR__.'/report.txt']);

        $this->assertNull($config->reportFile);
        $this->assertSame(['full' => null], $config->reports);

    }//end testReportFileDoesNotSetReportsCbf()


}//end class
