<?php
/**
 * Tests the support of PHP 8.1 "enum" keyword.
 *
 * @author    Jaroslav Hanslík <kukulich@kukulich.cz>
 * @copyright 2021 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

final class BackfillEnumTest extends AbstractTokenizerTestCase
{


    /**
     * Test that the "enum" keyword is tokenized as such.
     *
     * @param string $testMarker   The comment which prefaces the target token in the test file.
     * @param string $testContent  The token content to look for.
     * @param int    $openerOffset Offset to find expected scope opener.
     * @param int    $closerOffset Offset to find expected scope closer.
     *
     * @dataProvider dataEnums
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testEnums($testMarker, $testContent, $openerOffset, $closerOffset)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $enum       = $this->getTargetToken($testMarker, [T_ENUM, T_STRING], $testContent);
        $tokenArray = $tokens[$enum];

        $this->assertSame(T_ENUM, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_ENUM (code)');
        $this->assertSame('T_ENUM', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_ENUM (type)');

        $this->assertArrayHasKey('scope_condition', $tokenArray);
        $this->assertArrayHasKey('scope_opener', $tokenArray);
        $this->assertArrayHasKey('scope_closer', $tokenArray);

        $this->assertSame($enum, $tokenArray['scope_condition']);

        $scopeOpener = $tokenArray['scope_opener'];
        $scopeCloser = $tokenArray['scope_closer'];

        $expectedScopeOpener = ($enum + $openerOffset);
        $expectedScopeCloser = ($enum + $closerOffset);

        $this->assertSame($expectedScopeOpener, $scopeOpener);
        $this->assertArrayHasKey('scope_condition', $tokens[$scopeOpener]);
        $this->assertArrayHasKey('scope_opener', $tokens[$scopeOpener]);
        $this->assertArrayHasKey('scope_closer', $tokens[$scopeOpener]);
        $this->assertSame($enum, $tokens[$scopeOpener]['scope_condition']);
        $this->assertSame($scopeOpener, $tokens[$scopeOpener]['scope_opener']);
        $this->assertSame($scopeCloser, $tokens[$scopeOpener]['scope_closer']);

        $this->assertSame($expectedScopeCloser, $scopeCloser);
        $this->assertArrayHasKey('scope_condition', $tokens[$scopeCloser]);
        $this->assertArrayHasKey('scope_opener', $tokens[$scopeCloser]);
        $this->assertArrayHasKey('scope_closer', $tokens[$scopeCloser]);
        $this->assertSame($enum, $tokens[$scopeCloser]['scope_condition']);
        $this->assertSame($scopeOpener, $tokens[$scopeCloser]['scope_opener']);
        $this->assertSame($scopeCloser, $tokens[$scopeCloser]['scope_closer']);

    }//end testEnums()


    /**
     * Data provider.
     *
     * @see testEnums()
     *
     * @return array<string, array<string, string|int>>
     */
    public static function dataEnums()
    {
        return [
            'enum - pure'                                                                   => [
                'testMarker'   => '/* testPureEnum */',
                'testContent'  => 'enum',
                'openerOffset' => 4,
                'closerOffset' => 12,
            ],
            'enum - backed int'                                                             => [
                'testMarker'   => '/* testBackedIntEnum */',
                'testContent'  => 'enum',
                'openerOffset' => 7,
                'closerOffset' => 29,
            ],
            'enum - backed string'                                                          => [
                'testMarker'   => '/* testBackedStringEnum */',
                'testContent'  => 'enum',
                'openerOffset' => 8,
                'closerOffset' => 30,
            ],
            'enum - backed int + implements'                                                => [
                'testMarker'   => '/* testComplexEnum */',
                'testContent'  => 'enum',
                'openerOffset' => 11,
                'closerOffset' => 72,
            ],
            'enum keyword when "enum" is the name for the construct (yes, this is allowed)' => [
                'testMarker'   => '/* testEnumWithEnumAsClassName */',
                'testContent'  => 'enum',
                'openerOffset' => 6,
                'closerOffset' => 7,
            ],
            'enum - keyword is case insensitive'                                            => [
                'testMarker'   => '/* testEnumIsCaseInsensitive */',
                'testContent'  => 'EnUm',
                'openerOffset' => 4,
                'closerOffset' => 5,
            ],
            'enum - declaration containing comment'                                         => [
                'testMarker'   => '/* testDeclarationContainingComment */',
                'testContent'  => 'enum',
                'openerOffset' => 6,
                'closerOffset' => 14,
            ],
        ];

    }//end dataEnums()


    /**
     * Test that "enum" when not used as the keyword is still tokenized as `T_STRING`.
     *
     * @param string $testMarker  The comment which prefaces the target token in the test file.
     * @param string $testContent The token content to look for.
     *
     * @dataProvider dataNotEnums
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testNotEnums($testMarker, $testContent)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken($testMarker, [T_ENUM, T_STRING], $testContent);
        $tokenArray = $tokens[$target];

        $this->assertSame(T_STRING, $tokenArray['code'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (code)');
        $this->assertSame('T_STRING', $tokenArray['type'], 'Token tokenized as '.$tokenArray['type'].', not T_STRING (type)');

    }//end testNotEnums()


    /**
     * Data provider.
     *
     * @see testNotEnums()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataNotEnums()
    {
        return [
            'not enum - construct named enum'                            => [
                'testMarker'  => '/* testEnumAsClassNameAfterEnumKeyword */',
                'testContent' => 'Enum',
            ],
            'not enum - class named enum'                                => [
                'testMarker'  => '/* testEnumUsedAsClassName */',
                'testContent' => 'Enum',
            ],
            'not enum - class constant named enum'                       => [
                'testMarker'  => '/* testEnumUsedAsClassConstantName */',
                'testContent' => 'ENUM',
            ],
            'not enum - method named enum'                               => [
                'testMarker'  => '/* testEnumUsedAsMethodName */',
                'testContent' => 'enum',
            ],
            'not enum - class property named enum'                       => [
                'testMarker'  => '/* testEnumUsedAsPropertyName */',
                'testContent' => 'enum',
            ],
            'not enum - global function named enum'                      => [
                'testMarker'  => '/* testEnumUsedAsFunctionName */',
                'testContent' => 'enum',
            ],
            'not enum - namespace named enum'                            => [
                'testMarker'  => '/* testEnumUsedAsNamespaceName */',
                'testContent' => 'Enum',
            ],
            'not enum - part of namespace named enum'                    => [
                'testMarker'  => '/* testEnumUsedAsPartOfNamespaceName */',
                'testContent' => 'Enum',
            ],
            'not enum - class instantiation for class enum'              => [
                'testMarker'  => '/* testEnumUsedInObjectInitialization */',
                'testContent' => 'Enum',
            ],
            'not enum - function call'                                   => [
                'testMarker'  => '/* testEnumAsFunctionCall */',
                'testContent' => 'enum',
            ],
            'not enum - namespace relative function call'                => [
                'testMarker'  => '/* testEnumAsFunctionCallWithNamespace */',
                'testContent' => 'enum',
            ],
            'not enum - class constant fetch with enum as class name'    => [
                'testMarker'  => '/* testClassConstantFetchWithEnumAsClassName */',
                'testContent' => 'Enum',
            ],
            'not enum - class constant fetch with enum as constant name' => [
                'testMarker'  => '/* testClassConstantFetchWithEnumAsConstantName */',
                'testContent' => 'ENUM',
            ],
            'parse error, not enum - enum declaration without name'      => [
                'testMarker'  => '/* testParseErrorMissingName */',
                'testContent' => 'enum',
            ],
            'parse error, not enum - enum declaration with curlies'      => [
                'testMarker'  => '/* testParseErrorLiveCoding */',
                'testContent' => 'enum',
            ],
        ];

    }//end dataNotEnums()


}//end class
