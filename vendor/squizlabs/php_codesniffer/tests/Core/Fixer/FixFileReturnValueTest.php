<?php
/**
 * Tests for the Fixer::fixFile() return value.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Fixer;

use PHP_CodeSniffer\Files\LocalFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Fixer::fixFile() return value.
 *
 * @covers PHP_CodeSniffer\Fixer::fixFile
 */
final class FixFileReturnValueTest extends TestCase
{


    /**
     * Test that the return value of the fixFile() method is true when the file was completely fixed.
     *
     * @return void
     */
    public function testReturnValueIsTrueWhenFileWasFixed()
    {
        $standard = __DIR__.'/FixFileReturnValueAllGoodTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        $testCaseFile = __DIR__.'/Fixtures/test.inc';
        $phpcsFile    = new LocalFile($testCaseFile, $ruleset, $config);
        $phpcsFile->process();
        $fixed = $phpcsFile->fixer->fixFile();

        $this->assertTrue($fixed);

    }//end testReturnValueIsTrueWhenFileWasFixed()


    /**
     * Test that the return value of the fixFile() method is false when the file failed to make all fixes.
     *
     * @param string $standard The ruleset file to use for the test.
     *
     * @dataProvider dataReturnValueIsFalse
     *
     * @return void
     */
    public function testReturnValueIsFalse($standard)
    {
        $config  = new ConfigDouble(["--standard=$standard"]);
        $ruleset = new Ruleset($config);

        $testCaseFile = __DIR__.'/Fixtures/test.inc';
        $phpcsFile    = new LocalFile($testCaseFile, $ruleset, $config);
        $phpcsFile->process();
        $fixed = $phpcsFile->fixer->fixFile();

        $this->assertFalse($fixed);

    }//end testReturnValueIsFalse()


    /**
     * Data provider.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataReturnValueIsFalse()
    {
        return [
            'when there is a fixer conflict'                                    => [
                'standard' => __DIR__.'/FixFileReturnValueConflictTest.xml',
            ],
            'when the fixer ran out of loops before all fixes could be applied' => [
                'standard' => __DIR__.'/FixFileReturnValueNotEnoughLoopsTest.xml',
            ],
        ];

    }//end dataReturnValueIsFalse()


}//end class
