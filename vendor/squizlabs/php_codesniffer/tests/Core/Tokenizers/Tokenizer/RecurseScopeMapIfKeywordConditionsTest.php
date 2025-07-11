<?php
/**
 * Tests setting the scope for T_IF token.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @author    Rodrigo Primo <rodrigosprimo@gmail.com>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\Tokenizer;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class RecurseScopeMapIfKeywordConditionsTest extends AbstractTokenizerTestCase
{


    /**
     * Tests setting the scope for T_IF token with nested case statement missing break statement.
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/497#ref-commit-fddc61a
     *
     * @covers \PHP_CodeSniffer\Tokenizers\Tokenizer::recurseScopeMap
     *
     * @return void
     */
    public function testIfElseWithNestedCaseMissingBreakSharedClosers()
    {

        $tokens       = $this->phpcsFile->getTokens();
        $ifTestMarker = '/* testIfElseWithNestedCaseMissingBreak */';
        $ifCloserTestMarker = '/* testIfElseWithNestedCaseMissingBreakCloser */';
        $ifTokenIndex       = $this->getTargetToken($ifTestMarker, T_IF);
        $tokenArray         = $tokens[$ifTokenIndex];

        $expectedScopeCondition = $ifTokenIndex;
        $expectedScopeOpener    = $this->getTargetToken($ifTestMarker, T_COLON);
        $expectedScopeCloser    = $this->getTargetToken($ifCloserTestMarker, T_ELSE);

        $this->assertArrayHasKey('scope_condition', $tokenArray, 'Scope condition not set');
        $this->assertSame(
            $expectedScopeCondition,
            $tokenArray['scope_condition'],
            sprintf(
                'Scope condition not set correctly; expected T_IF, found %s',
                $tokens[$tokenArray['scope_condition']]['type']
            )
        );

        $this->assertArrayHasKey('scope_opener', $tokenArray, 'Scope opener not set');
        $this->assertSame(
            $expectedScopeOpener,
            $tokenArray['scope_opener'],
            sprintf(
                'Scope opener not set correctly; expected %s, found %s',
                $tokens[$expectedScopeOpener]['type'],
                $tokens[$tokenArray['scope_opener']]['type']
            )
        );

        $this->assertArrayHasKey('scope_closer', $tokenArray, 'Scope closer not set');
        $this->assertSame(
            $expectedScopeCloser,
            $tokenArray['scope_closer'],
            sprintf(
                'Scope closer not set correctly; expected %s, found %s',
                $tokens[$expectedScopeCloser]['type'],
                $tokens[$tokenArray['scope_closer']]['type']
            )
        );

    }//end testIfElseWithNestedCaseMissingBreakSharedClosers()


}//end class
