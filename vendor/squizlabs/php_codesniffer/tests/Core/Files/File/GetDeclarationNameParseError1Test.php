<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File::getDeclarationName method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2025 PHPCSStandards Contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Files\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

/**
 * Tests for the \PHP_CodeSniffer\Files\File:getDeclarationName method.
 *
 * @covers \PHP_CodeSniffer\Files\File::getDeclarationName
 */
final class GetDeclarationNameParseError1Test extends AbstractMethodUnitTest
{


    /**
     * Test receiving "null" in case of a parse error.
     *
     * @return void
     */
    public function testGetDeclarationNameNull()
    {
        $target = $this->getTargetToken('/* testLiveCoding */', T_FUNCTION);
        $result = self::$phpcsFile->getDeclarationName($target);
        $this->assertNull($result);

    }//end testGetDeclarationNameNull()


}//end class
