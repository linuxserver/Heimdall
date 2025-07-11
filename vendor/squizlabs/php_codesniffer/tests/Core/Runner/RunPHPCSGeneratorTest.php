<?php
/**
 * Tests the wiring in of the Generator functionality in the Runner class.
 *
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Runner;

use PHP_CodeSniffer\Runner;
use PHP_CodeSniffer\Tests\Core\Runner\AbstractRunnerTestCase;

/**
 * Tests the wiring in of the Generator functionality in the Runner class.
 *
 * @covers \PHP_CodeSniffer\Runner::runPHPCS
 * @group  Windows
 */
final class RunPHPCSGeneratorTest extends AbstractRunnerTestCase
{


    /**
     * Test that the documentation for each standard passed on the command-line is shown separately.
     *
     * @return void
     */
    public function testGeneratorWillShowEachStandardSeparately()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $standard        = dirname(__DIR__).'/Generators/OneDocTest.xml';
        $_SERVER['argv'] = [
            'phpcs',
            '--generator=Text',
            "--standard=$standard,PSR1",
            '--report-width=80',
        ];

        $regex = '`^
            \R*                                                      # Optional blank line at the start.
            (?:
                (?P<delimiter>-++\R)                                 # Line with dashes.
                \|[ ]GENERATORTEST[ ]CODING[ ]STANDARD:[ ][^\|]+\|\R # Doc title line with prefix expected for first standard.
                (?P>delimiter)                                       # Line with dashes.
                \R(?:[^\r\n]+\R)+\R{2}                               # Standard description.
            )                                                        # Only expect this group once.
            (?:
                (?P>delimiter)                                       # Line with dashes.
                \|[ ]PSR1[ ]CODING[ ]STANDARD:[ ][^\|]+\|\R          # Doc title line with prefix expected for second standard.
                (?P>delimiter)                                       # Line with dashes.
                \R(?:[^\r\n]+\R)+\R                                  # Standard description.
                (?:
                    -+[ ]CODE[ ]COMPARISON[ ]-+\R                    # Code Comparison starter line with dashes.
                    (?:(?:[^\r\n]+\R)+(?P>delimiter)){2}             # Arbitrary text followed by a delimiter line.
                )*                                                   # Code comparison is optional and can exist multiple times.
                \R+
            ){3,}                                                    # This complete group should occur at least three times.
            `x';

        $this->expectOutputRegex($regex);

        $runner = new Runner();
        $runner->runPHPCS();

    }//end testGeneratorWillShowEachStandardSeparately()


}//end class
