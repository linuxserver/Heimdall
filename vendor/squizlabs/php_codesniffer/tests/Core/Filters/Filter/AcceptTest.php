<?php
/**
 * Tests for the \PHP_CodeSniffer\Filters\Filter::accept method.
 *
 * @author    Willington Vega <wvega@wvega.com>
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Filters\Filter;

use PHP_CodeSniffer\Filters\Filter;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Core\Filters\AbstractFilterTestCase;
use RecursiveArrayIterator;

/**
 * Tests for the \PHP_CodeSniffer\Filters\Filter::accept method.
 *
 * @covers \PHP_CodeSniffer\Filters\Filter
 */
final class AcceptTest extends AbstractFilterTestCase
{


    /**
     * Initialize the config and ruleset objects based on the `AcceptTest.xml` ruleset file.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function initializeConfigAndRuleset()
    {
        $standard      = __DIR__.'/'.basename(__FILE__, '.php').'.xml';
        self::$config  = new ConfigDouble(["--standard=$standard", '--ignore=*/somethingelse/*']);
        self::$ruleset = new Ruleset(self::$config);

    }//end initializeConfigAndRuleset()


    /**
     * Test filtering a file list for excluded paths.
     *
     * @param array<string> $inputPaths     List of file paths to be filtered.
     * @param array<string> $expectedOutput Expected filtering result.
     *
     * @dataProvider dataExcludePatterns
     *
     * @return void
     */
    public function testExcludePatterns($inputPaths, $expectedOutput)
    {
        $fakeDI = new RecursiveArrayIterator($inputPaths);
        $filter = new Filter($fakeDI, '/', self::$config, self::$ruleset);

        $this->assertSame($expectedOutput, $this->getFilteredResultsAsArray($filter));

    }//end testExcludePatterns()


    /**
     * Data provider.
     *
     * @see testExcludePatterns
     *
     * @return array<string, array<string, array<string>>>
     */
    public static function dataExcludePatterns()
    {
        $testCases = [
            // Test top-level exclude patterns.
            'Non-sniff specific path based excludes from ruleset and command line are respected and don\'t filter out too much' => [
                'inputPaths'     => [
                    '/path/to/src/Main.php',
                    '/path/to/src/Something/Main.php',
                    '/path/to/src/Somethingelse/Main.php',
                    '/path/to/src/SomethingelseEvenLonger/Main.php',
                    '/path/to/src/Other/Main.php',
                ],
                'expectedOutput' => [
                    '/path/to/src/Main.php',
                    '/path/to/src/SomethingelseEvenLonger/Main.php',
                ],
            ],

            // Test ignoring standard/sniff specific exclude patterns.
            'Filter should not act on standard/sniff specific exclude patterns'                                                 => [
                'inputPaths'     => [
                    '/path/to/src/generic-project/Main.php',
                    '/path/to/src/generic/Main.php',
                    '/path/to/src/anything-generic/Main.php',
                ],
                'expectedOutput' => [
                    '/path/to/src/generic-project/Main.php',
                    '/path/to/src/generic/Main.php',
                    '/path/to/src/anything-generic/Main.php',
                ],
            ],
        ];

        // Allow these tests to work on Windows as well.
        return self::mapPathsToRuntimeOs($testCases);

    }//end dataExcludePatterns()


}//end class
