<?php
/**
 * Tests converting enum "case" to T_ENUM_CASE.
 *
 * @author    Jaroslav Hanslík <kukulich@kukulich.cz>
 * @copyright 2021 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

final class EnumCaseTest extends AbstractTokenizerTestCase
{


    /**
     * Test that the enum "case" is converted to T_ENUM_CASE.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataEnumCases
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testEnumCases($testMarker)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $enumCase   = $this->getTargetToken($testMarker, [T_ENUM_CASE, T_CASE]);
        $tokenArray = $tokens[$enumCase];

        $this->assertSame(T_ENUM_CASE, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_ENUM_CASE (code)');
        $this->assertSame('T_ENUM_CASE', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_ENUM_CASE (type)');

    }//end testEnumCases()


    /**
     * Data provider.
     *
     * @see testEnumCases()
     *
     * @return array<string, array<string>>
     */
    public static function dataEnumCases()
    {
        return [
            'enum case, no value'                                        => ['/* testPureEnumCase */'],
            'enum case, integer value'                                   => ['/* testBackingIntegerEnumCase */'],
            'enum case, string value'                                    => ['/* testBackingStringEnumCase */'],
            'enum case, integer value in more complex enum'              => ['/* testEnumCaseInComplexEnum */'],
            'enum case, keyword in mixed case'                           => ['/* testEnumCaseIsCaseInsensitive */'],
            'enum case, after switch statement'                          => ['/* testEnumCaseAfterSwitch */'],
            'enum case, after switch statement using alternative syntax' => ['/* testEnumCaseAfterSwitchWithEndSwitch */'],
        ];

    }//end dataEnumCases()


    /**
     * Test that "case" that is not enum case is still tokenized as `T_CASE`.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataNotEnumCases
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testNotEnumCases($testMarker)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $case       = $this->getTargetToken($testMarker, [T_ENUM_CASE, T_CASE]);
        $tokenArray = $tokens[$case];

        $this->assertSame(T_CASE, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_CASE (code)');
        $this->assertSame('T_CASE', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_CASE (type)');

    }//end testNotEnumCases()


    /**
     * Data provider.
     *
     * @see testNotEnumCases()
     *
     * @return array<string, array<string>>
     */
    public static function dataNotEnumCases()
    {
        return [
            'switch case with constant, semicolon condition end' => ['/* testCaseWithSemicolonIsNotEnumCase */'],
            'switch case with constant, colon condition end'     => ['/* testCaseWithConstantIsNotEnumCase */'],
            'switch case with constant, comparison'              => ['/* testCaseWithConstantAndIdenticalIsNotEnumCase */'],
            'switch case with constant, assignment'              => ['/* testCaseWithAssigmentToConstantIsNotEnumCase */'],
            'switch case with constant, keyword in mixed case'   => ['/* testIsNotEnumCaseIsCaseInsensitive */'],
            'switch case, body in curlies declares enum'         => ['/* testCaseInSwitchWhenCreatingEnumInSwitch1 */'],
            'switch case, body after semicolon declares enum'    => ['/* testCaseInSwitchWhenCreatingEnumInSwitch2 */'],
        ];

    }//end dataNotEnumCases()


    /**
     * Test that "case" that is not enum case is still tokenized as `T_CASE`.
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @dataProvider dataKeywordAsEnumCaseNameShouldBeString
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testKeywordAsEnumCaseNameShouldBeString($testMarker)
    {
        $tokens       = $this->phpcsFile->getTokens();
        $enumCaseName = $this->getTargetToken($testMarker, [T_STRING, T_INTERFACE, T_TRAIT, T_ENUM, T_FUNCTION, T_FALSE, T_DEFAULT, T_ARRAY]);
        $tokenArray   = $tokens[$enumCaseName];

        $this->assertSame(T_STRING, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (code)');
        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (type)');

    }//end testKeywordAsEnumCaseNameShouldBeString()


    /**
     * Data provider.
     *
     * @see testKeywordAsEnumCaseNameShouldBeString()
     *
     * @return array<string, array<string>>
     */
    public static function dataKeywordAsEnumCaseNameShouldBeString()
    {
        return [
            '"interface" as case name' => ['/* testKeywordAsEnumCaseNameShouldBeString1 */'],
            '"trait" as case name'     => ['/* testKeywordAsEnumCaseNameShouldBeString2 */'],
            '"enum" as case name'      => ['/* testKeywordAsEnumCaseNameShouldBeString3 */'],
            '"function" as case name'  => ['/* testKeywordAsEnumCaseNameShouldBeString4 */'],
            '"false" as case name'     => ['/* testKeywordAsEnumCaseNameShouldBeString5 */'],
            '"default" as case name'   => ['/* testKeywordAsEnumCaseNameShouldBeString6 */'],
            '"array" as case name'     => ['/* testKeywordAsEnumCaseNameShouldBeString7 */'],
        ];

    }//end dataKeywordAsEnumCaseNameShouldBeString()


}//end class
