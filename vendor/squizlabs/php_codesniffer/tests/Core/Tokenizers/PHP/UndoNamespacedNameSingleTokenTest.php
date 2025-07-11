<?php
/**
 * Tests the tokenization of identifier names.
 *
 * As of PHP 8, identifier names are tokenized differently, depending on them being
 * either fully qualified, partially qualified or relative to the current namespace.
 *
 * This test file safeguards that in PHPCS 3.x this new form of tokenization is "undone"
 * and the tokenization of these identifier names is the same in all PHP versions
 * based on how these names were tokenized in PHP 5/7.
 *
 * {@link https://wiki.php.net/rfc/namespaced_names_as_token}
 * {@link https://github.com/squizlabs/PHP_CodeSniffer/issues/3041}
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2020 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;
use PHP_CodeSniffer\Util\Tokens;

final class UndoNamespacedNameSingleTokenTest extends AbstractTokenizerTestCase
{


    /**
     * Test that identifier names are tokenized the same across PHP versions, based on the PHP 5/7 tokenization.
     *
     * @param string                       $testMarker     The comment prefacing the test.
     * @param array<array<string, string>> $expectedTokens The tokenization expected.
     *
     * @dataProvider dataIdentifierTokenization
     * @covers       PHP_CodeSniffer\Tokenizers\PHP::tokenize
     *
     * @return void
     */
    public function testIdentifierTokenization($testMarker, $expectedTokens)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $identifier = $this->getTargetToken($testMarker, constant($expectedTokens[0]['type']));

        foreach ($expectedTokens as $tokenInfo) {
            $this->assertSame(
                constant($tokenInfo['type']),
                $tokens[$identifier]['code'],
                'Token tokenized as '.Tokens::tokenName($tokens[$identifier]['code']).', not '.$tokenInfo['type'].' (code)'
            );
            $this->assertSame(
                $tokenInfo['type'],
                $tokens[$identifier]['type'],
                'Token tokenized as '.$tokens[$identifier]['type'].', not '.$tokenInfo['type'].' (type)'
            );
            $this->assertSame($tokenInfo['content'], $tokens[$identifier]['content']);

            ++$identifier;
        }

    }//end testIdentifierTokenization()


    /**
     * Data provider.
     *
     * @see testIdentifierTokenization()
     *
     * @return array<string, array<string, string|array<array<string, string>>>>
     */
    public static function dataIdentifierTokenization()
    {
        return [
            'namespace declaration'                                                                       => [
                'testMarker'     => '/* testNamespaceDeclaration */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Package',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            'namespace declaration, multi-level'                                                          => [
                'testMarker'     => '/* testNamespaceDeclarationWithLevels */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Vendor',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'SubLevel',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Domain',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            'import use statement, class'                                                                 => [
                'testMarker'     => '/* testUseStatement */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            'import use statement, class, multi-level'                                                    => [
                'testMarker'     => '/* testUseStatementWithLevels */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Vendor',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Level',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Domain',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            'import use statement, function'                                                              => [
                'testMarker'     => '/* testFunctionUseStatement */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_name',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            'import use statement, function, multi-level'                                                 => [
                'testMarker'     => '/* testFunctionUseStatementWithLevels */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Vendor',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Level',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_in_ns',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            'import use statement, constant'                                                              => [
                'testMarker'     => '/* testConstantUseStatement */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'const',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'CONSTANT_NAME',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            'import use statement, constant, multi-level'                                                 => [
                'testMarker'     => '/* testConstantUseStatementWithLevels */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'const',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Vendor',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Level',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'OTHER_CONSTANT',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            'import use statement, multi-statement, unqualified class'                                    => [
                'testMarker'     => '/* testMultiUseUnqualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'UnqualifiedClassName',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                ],
            ],
            'import use statement, multi-statement, partially qualified class'                            => [
                'testMarker'     => '/* testMultiUsePartiallyQualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Sublevel',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'PartiallyClassName',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            'group use statement, multi-level prefix, mix inside group'                                   => [
                'testMarker'     => '/* testGroupUseStatement */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Vendor',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Level',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_OPEN_USE_GROUP',
                        'content' => '{',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'AnotherDomain',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_grouped',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'const',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'CONSTANT_GROUPED',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Sub',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'YetAnotherDomain',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'SubLevelA',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_grouped_too',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'const',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'SubLevelB',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'CONSTANT_GROUPED_TOO',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_CLOSE_USE_GROUP',
                        'content' => '}',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            'class declaration'                                                                           => [
                'testMarker'     => '/* testClassName */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'MyClass',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'class declaration, extends fully qualified name'                                             => [
                'testMarker'     => '/* testExtendedFQN */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Vendor',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Level',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'FQN',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'class declaration, implements namespace relative name'                                       => [
                'testMarker'     => '/* testImplementsRelative */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NAMESPACE',
                        'content' => 'namespace',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Name',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                ],
            ],
            'class declaration, implements fully qualified name'                                          => [
                'testMarker'     => '/* testImplementsFQN */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Fully',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Qualified',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                ],
            ],
            'class declaration, implements unqualified name'                                              => [
                'testMarker'     => '/* testImplementsUnqualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Unqualified',
                    ],
                    [
                        'type'    => 'T_COMMA',
                        'content' => ',',
                    ],
                ],
            ],
            'class declaration, implements partially qualified name'                                      => [
                'testMarker'     => '/* testImplementsPartiallyQualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Sub',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Level',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Name',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
            'method declaration'                                                                          => [
                'testMarker'     => '/* testFunctionName */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_name',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            'param type declaration, namespace relative name'                                             => [
                'testMarker'     => '/* testTypeDeclarationRelative */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NAMESPACE',
                        'content' => 'namespace',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Name',
                    ],
                    [
                        'type'    => 'T_TYPE_UNION',
                        'content' => '|',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'object',
                    ],
                ],
            ],
            'param type declaration, fully qualified name'                                                => [
                'testMarker'     => '/* testTypeDeclarationFQN */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Fully',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Qualified',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Name',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            'param type declaration, unqualified name'                                                    => [
                'testMarker'     => '/* testTypeDeclarationUnqualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Unqualified',
                    ],
                    [
                        'type'    => 'T_TYPE_UNION',
                        'content' => '|',
                    ],
                    [
                        'type'    => 'T_FALSE',
                        'content' => 'false',
                    ],
                ],
            ],
            'param type declaration, partially qualified name'                                            => [
                'testMarker'     => '/* testTypeDeclarationPartiallyQualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NULLABLE',
                        'content' => '?',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Sublevel',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Name',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            'return type declaration, fully qualified name'                                               => [
                'testMarker'     => '/* testReturnTypeFQN */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NULLABLE',
                        'content' => '?',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Name',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            'function call, namespace relative name'                                                      => [
                'testMarker'     => '/* testFunctionCallRelative */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NAMESPACE',
                        'content' => 'NameSpace',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_name',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            'function call, fully qualified name'                                                         => [
                'testMarker'     => '/* testFunctionCallFQN */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Vendor',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Package',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_name',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            'function call, unqualified name'                                                             => [
                'testMarker'     => '/* testFunctionCallUnqualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_name',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            'function call, partially qualified name'                                                     => [
                'testMarker'     => '/* testFunctionCallPartiallyQualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Level',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_name',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            'catch, namespace relative name'                                                              => [
                'testMarker'     => '/* testCatchRelative */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NAMESPACE',
                        'content' => 'namespace',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'SubLevel',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Exception',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            'catch, fully qualified name'                                                                 => [
                'testMarker'     => '/* testCatchFQN */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Exception',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            'catch, unqualified name'                                                                     => [
                'testMarker'     => '/* testCatchUnqualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Exception',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            'catch, partially qualified name'                                                             => [
                'testMarker'     => '/* testCatchPartiallyQualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Level',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Exception',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            'class instantiation, namespace relative name'                                                => [
                'testMarker'     => '/* testNewRelative */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NAMESPACE',
                        'content' => 'namespace',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            'class instantiation, fully qualified name'                                                   => [
                'testMarker'     => '/* testNewFQN */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Vendor',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            'class instantiation, unqualified name'                                                       => [
                'testMarker'     => '/* testNewUnqualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            'class instantiation, partially qualified name'                                               => [
                'testMarker'     => '/* testNewPartiallyQualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Level',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            'double colon class access, namespace relative name'                                          => [
                'testMarker'     => '/* testDoubleColonRelative */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NAMESPACE',
                        'content' => 'namespace',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_DOUBLE_COLON',
                        'content' => '::',
                    ],
                ],
            ],
            'double colon class access, fully qualified name'                                             => [
                'testMarker'     => '/* testDoubleColonFQN */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_DOUBLE_COLON',
                        'content' => '::',
                    ],
                ],
            ],
            'double colon class access, unqualified name'                                                 => [
                'testMarker'     => '/* testDoubleColonUnqualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_DOUBLE_COLON',
                        'content' => '::',
                    ],
                ],
            ],
            'double colon class access, partially qualified name'                                         => [
                'testMarker'     => '/* testDoubleColonPartiallyQualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Level',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_DOUBLE_COLON',
                        'content' => '::',
                    ],
                ],
            ],
            'instanceof, namespace relative name'                                                         => [
                'testMarker'     => '/* testInstanceOfRelative */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NAMESPACE',
                        'content' => 'namespace',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            'instanceof, fully qualified name'                                                            => [
                'testMarker'     => '/* testInstanceOfFQN */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Full',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_CLOSE_PARENTHESIS',
                        'content' => ')',
                    ],
                ],
            ],
            'instanceof, unqualified name'                                                                => [
                'testMarker'     => '/* testInstanceOfUnqualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                ],
            ],
            'instanceof, partially qualified name'                                                        => [
                'testMarker'     => '/* testInstanceOfPartiallyQualified */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Partially',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'ClassName',
                    ],
                    [
                        'type'    => 'T_SEMICOLON',
                        'content' => ';',
                    ],
                ],
            ],
            'function call, namespace relative, with whitespace (invalid in PHP 8)'                       => [
                'testMarker'     => '/* testInvalidInPHP8Whitespace */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NAMESPACE',
                        'content' => 'namespace',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Sublevel',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '          ',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => ' ',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'function_name',
                    ],
                    [
                        'type'    => 'T_OPEN_PARENTHESIS',
                        'content' => '(',
                    ],
                ],
            ],
            'double colon class access, fully qualified, with whitespace and comments (invalid in PHP 8)' => [
                'testMarker'     => '/* testInvalidInPHP8Comments */',
                'expectedTokens' => [
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Fully',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_PHPCS_IGNORE',
                        'content' => '// phpcs:ignore Stnd.Cat.Sniff -- for reasons
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Qualified',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_COMMENT',
                        'content' => '/* comment */',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '    ',
                    ],
                    [
                        'type'    => 'T_NS_SEPARATOR',
                        'content' => '\\',
                    ],
                    [
                        'type'    => 'T_STRING',
                        'content' => 'Name',
                    ],
                    [
                        'type'    => 'T_WHITESPACE',
                        'content' => '
',
                    ],
                ],
            ],
        ];

    }//end dataIdentifierTokenization()


}//end class
