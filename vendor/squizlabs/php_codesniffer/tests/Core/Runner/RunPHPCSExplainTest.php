<?php
/**
 * Tests the wiring in of the explain functionality in the Runner class.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2023 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Runner;

use PHP_CodeSniffer\Runner;
use PHP_CodeSniffer\Tests\Core\Runner\AbstractRunnerTestCase;

/**
 * Tests the wiring in of the explain functionality in the Runner class.
 *
 * @covers \PHP_CodeSniffer\Runner::runPHPCS
 */
final class RunPHPCSExplainTest extends AbstractRunnerTestCase
{


    /**
     * Test that each standard passed on the command-line is explained separately.
     *
     * @return void
     */
    public function testExplainWillExplainEachStandardSeparately()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $standard        = dirname(__DIR__).'/Ruleset/ExplainSingleSniffTest.xml';
        $_SERVER['argv'] = [
            'phpcs',
            '-e',
            "--standard=PSR1,$standard",
            '--report-width=80',
        ];

        $expected  = PHP_EOL;
        $expected .= 'The PSR1 standard contains 8 sniffs'.PHP_EOL.PHP_EOL;
        $expected .= 'Generic (4 sniffs)'.PHP_EOL;
        $expected .= '------------------'.PHP_EOL;
        $expected .= '  Generic.Files.ByteOrderMark'.PHP_EOL;
        $expected .= '  Generic.NamingConventions.UpperCaseConstantName'.PHP_EOL;
        $expected .= '  Generic.PHP.DisallowAlternativePHPTags'.PHP_EOL;
        $expected .= '  Generic.PHP.DisallowShortOpenTag'.PHP_EOL.PHP_EOL;
        $expected .= 'PSR1 (3 sniffs)'.PHP_EOL;
        $expected .= '---------------'.PHP_EOL;
        $expected .= '  PSR1.Classes.ClassDeclaration'.PHP_EOL;
        $expected .= '  PSR1.Files.SideEffects'.PHP_EOL;
        $expected .= '  PSR1.Methods.CamelCapsMethodName'.PHP_EOL.PHP_EOL;
        $expected .= 'Squiz (1 sniff)'.PHP_EOL;
        $expected .= '---------------'.PHP_EOL;
        $expected .= '  Squiz.Classes.ValidClassName'.PHP_EOL.PHP_EOL;

        $expected .= 'The ExplainSingleSniffTest standard contains 1 sniff'.PHP_EOL.PHP_EOL;
        $expected .= 'Squiz (1 sniff)'.PHP_EOL;
        $expected .= '---------------'.PHP_EOL;
        $expected .= '  Squiz.Scope.MethodScope'.PHP_EOL;

        $this->expectOutputString($expected);

        $runner = new Runner();
        $runner->runPHPCS();

    }//end testExplainWillExplainEachStandardSeparately()


}//end class
