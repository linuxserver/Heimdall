<?php
/**
 * Tests the support of PHP 8.4 asymmetric visibility.
 *
 * @author    Daniel Scherzer <daniel.e.scherzer@gmail.com>
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizers\PHP;

use PHP_CodeSniffer\Tests\Core\Tokenizers\AbstractTokenizerTestCase;

/**
 * Tests the support of PHP 8.4 asymmetric visibility.
 *
 * @covers PHP_CodeSniffer\Tokenizers\PHP::processAdditional
 */
final class BackfillAsymmetricVisibilityTest extends AbstractTokenizerTestCase
{


    /**
     * Test that the asymmetric visibility keywords are tokenized as such.
     *
     * @param string $testMarker  The comment which prefaces the target token in the test file.
     * @param string $testType    The expected token type
     * @param string $testContent The token content to look for
     *
     * @dataProvider dataAsymmetricVisibility
     *
     * @return void
     */
    public function testAsymmetricVisibility($testMarker, $testType, $testContent)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken(
            $testMarker,
            [
                T_PUBLIC_SET,
                T_PROTECTED_SET,
                T_PRIVATE_SET,
            ]
        );
        $tokenArray = $tokens[$target];

        $this->assertSame(
            $testType,
            $tokenArray['type'],
            'Token tokenized as '.$tokenArray['type'].' (type)'
        );
        $this->assertSame(
            constant($testType),
            $tokenArray['code'],
            'Token tokenized as '.$tokenArray['type'].' (code)'
        );
        $this->assertSame(
            $testContent,
            $tokenArray['content'],
            'Token tokenized as '.$tokenArray['type'].' (content)'
        );

    }//end testAsymmetricVisibility()


    /**
     * Data provider.
     *
     * @see testAsymmetricVisibility()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataAsymmetricVisibility()
    {
        return [
            // Normal property declarations.
            'property, public set, no read visibility, lowercase'      => [
                'testMarker'  => '/* testPublicSetProperty */',
                'testType'    => 'T_PUBLIC_SET',
                'testContent' => 'public(set)',
            ],
            'property, public set, no read visibility, uppercase'      => [
                'testMarker'  => '/* testPublicSetPropertyUC */',
                'testType'    => 'T_PUBLIC_SET',
                'testContent' => 'PUBLIC(SET)',
            ],
            'property, public set, has read visibility, lowercase'     => [
                'testMarker'  => '/* testPublicPublicSetProperty */',
                'testType'    => 'T_PUBLIC_SET',
                'testContent' => 'public(set)',
            ],
            'property, public set, has read visibility, uppercase'     => [
                'testMarker'  => '/* testPublicPublicSetPropertyUC */',
                'testType'    => 'T_PUBLIC_SET',
                'testContent' => 'PUBLIC(SET)',
            ],
            'property, protected set, no read visibility, lowercase'   => [
                'testMarker'  => '/* testProtectedSetProperty */',
                'testType'    => 'T_PROTECTED_SET',
                'testContent' => 'protected(set)',
            ],
            'property, protected set, no read visibility, uppercase'   => [
                'testMarker'  => '/* testProtectedSetPropertyUC */',
                'testType'    => 'T_PROTECTED_SET',
                'testContent' => 'PROTECTED(SET)',
            ],
            'property, protected set, has read visibility, lowercase'  => [
                'testMarker'  => '/* testPublicProtectedSetProperty */',
                'testType'    => 'T_PROTECTED_SET',
                'testContent' => 'protected(set)',
            ],
            'property, protected set, has read visibility, uppercase'  => [
                'testMarker'  => '/* testPublicProtectedSetPropertyUC */',
                'testType'    => 'T_PROTECTED_SET',
                'testContent' => 'PROTECTED(SET)',
            ],
            'property, private set, no read visibility, lowercase'     => [
                'testMarker'  => '/* testPrivateSetProperty */',
                'testType'    => 'T_PRIVATE_SET',
                'testContent' => 'private(set)',
            ],
            'property, private set, no read visibility, uppercase'     => [
                'testMarker'  => '/* testPrivateSetPropertyUC */',
                'testType'    => 'T_PRIVATE_SET',
                'testContent' => 'PRIVATE(SET)',
            ],
            'property, private set, has read visibility, lowercase'    => [
                'testMarker'  => '/* testPublicPrivateSetProperty */',
                'testType'    => 'T_PRIVATE_SET',
                'testContent' => 'private(set)',
            ],
            'property, private set, has read visibility, uppercase'    => [
                'testMarker'  => '/* testPublicPrivateSetPropertyUC */',
                'testType'    => 'T_PRIVATE_SET',
                'testContent' => 'PRIVATE(SET)',
            ],

            // Constructor property promotion.
            'promotion, public set, no read visibility, lowercase'     => [
                'testMarker'  => '/* testPublicSetCPP */',
                'testType'    => 'T_PUBLIC_SET',
                'testContent' => 'public(set)',
            ],
            'promotion, public set, no read visibility, uppercase'     => [
                'testMarker'  => '/* testPublicSetCPPUC */',
                'testType'    => 'T_PUBLIC_SET',
                'testContent' => 'PUBLIC(SET)',
            ],
            'promotion, public set, has read visibility, lowercase'    => [
                'testMarker'  => '/* testPublicPublicSetCPP */',
                'testType'    => 'T_PUBLIC_SET',
                'testContent' => 'public(set)',
            ],
            'promotion, public set, has read visibility, uppercase'    => [
                'testMarker'  => '/* testPublicPublicSetCPPUC */',
                'testType'    => 'T_PUBLIC_SET',
                'testContent' => 'PUBLIC(SET)',
            ],
            'promotion, protected set, no read visibility, lowercase'  => [
                'testMarker'  => '/* testProtectedSetCPP */',
                'testType'    => 'T_PROTECTED_SET',
                'testContent' => 'protected(set)',
            ],
            'promotion, protected set, no read visibility, uppercase'  => [
                'testMarker'  => '/* testProtectedSetCPPUC */',
                'testType'    => 'T_PROTECTED_SET',
                'testContent' => 'PROTECTED(SET)',
            ],
            'promotion, protected set, has read visibility, lowercase' => [
                'testMarker'  => '/* testPublicProtectedSetCPP */',
                'testType'    => 'T_PROTECTED_SET',
                'testContent' => 'protected(set)',
            ],
            'promotion, protected set, has read visibility, uppercase' => [
                'testMarker'  => '/* testPublicProtectedSetCPPUC */',
                'testType'    => 'T_PROTECTED_SET',
                'testContent' => 'PROTECTED(SET)',
            ],
            'promotion, private set, no read visibility, lowercase'    => [
                'testMarker'  => '/* testPrivateSetCPP */',
                'testType'    => 'T_PRIVATE_SET',
                'testContent' => 'private(set)',
            ],
            'promotion, private set, no read visibility, uppercase'    => [
                'testMarker'  => '/* testPrivateSetCPPUC */',
                'testType'    => 'T_PRIVATE_SET',
                'testContent' => 'PRIVATE(SET)',
            ],
            'promotion, private set, has read visibility, lowercase'   => [
                'testMarker'  => '/* testPublicPrivateSetCPP */',
                'testType'    => 'T_PRIVATE_SET',
                'testContent' => 'private(set)',
            ],
            'promotion, private set, has read visibility, uppercase'   => [
                'testMarker'  => '/* testPublicPrivateSetCPPUC */',
                'testType'    => 'T_PRIVATE_SET',
                'testContent' => 'PRIVATE(SET)',
            ],
        ];

    }//end dataAsymmetricVisibility()


    /**
     * Test that things that are not asymmetric visibility keywords are not
     * tokenized as such.
     *
     * @param string $testMarker  The comment which prefaces the target token in the test file.
     * @param string $testType    The expected token type
     * @param string $testContent The token content to look for
     *
     * @dataProvider dataNotAsymmetricVisibility
     *
     * @return void
     */
    public function testNotAsymmetricVisibility($testMarker, $testType, $testContent)
    {
        $tokens     = $this->phpcsFile->getTokens();
        $target     = $this->getTargetToken(
            $testMarker,
            [constant($testType)],
            $testContent
        );
        $tokenArray = $tokens[$target];

        $this->assertSame(
            $testType,
            $tokenArray['type'],
            'Token tokenized as '.$tokenArray['type'].' (type)'
        );
        $this->assertSame(
            constant($testType),
            $tokenArray['code'],
            'Token tokenized as '.$tokenArray['type'].' (code)'
        );

    }//end testNotAsymmetricVisibility()


    /**
     * Data provider.
     *
     * @see testNotAsymmetricVisibility()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataNotAsymmetricVisibility()
    {
        return [
            'property, invalid case 1'   => [
                'testMarker'  => '/* testInvalidUnsetProperty */',
                'testType'    => 'T_PUBLIC',
                'testContent' => 'public',
            ],
            'property, invalid case 2'   => [
                'testMarker'  => '/* testInvalidSpaceProperty */',
                'testType'    => 'T_PUBLIC',
                'testContent' => 'public',
            ],
            'property, invalid case 3'   => [
                'testMarker'  => '/* testInvalidCommentProperty */',
                'testType'    => 'T_PROTECTED',
                'testContent' => 'protected',
            ],
            'property, invalid case 4'   => [
                'testMarker'  => '/* testInvalidGetProperty */',
                'testType'    => 'T_PRIVATE',
                'testContent' => 'private',
            ],
            'property, invalid case 5'   => [
                'testMarker'  => '/* testInvalidNoParenProperty */',
                'testType'    => 'T_PRIVATE',
                'testContent' => 'private',
            ],

            // Constructor property promotion.
            'promotion, invalid case 1'  => [
                'testMarker'  => '/* testInvalidUnsetCPP */',
                'testType'    => 'T_PUBLIC',
                'testContent' => 'public',
            ],
            'promotion, invalid case 2'  => [
                'testMarker'  => '/* testInvalidSpaceCPP */',
                'testType'    => 'T_PUBLIC',
                'testContent' => 'public',
            ],
            'promotion, invalid case 3'  => [
                'testMarker'  => '/* testInvalidCommentCPP */',
                'testType'    => 'T_PROTECTED',
                'testContent' => 'protected',
            ],
            'promotion, invalid case 4'  => [
                'testMarker'  => '/* testInvalidGetCPP */',
                'testType'    => 'T_PRIVATE',
                'testContent' => 'private',
            ],
            'promotion, invalid case 5'  => [
                'testMarker'  => '/* testInvalidNoParenCPP */',
                'testType'    => 'T_PRIVATE',
                'testContent' => 'private',
            ],

            // Context sensitivitiy.
            'protected as function name' => [
                'testMarker'  => '/* testProtectedFunctionName */',
                'testType'    => 'T_STRING',
                'testContent' => 'protected',
            ],
            'public as function name'    => [
                'testMarker'  => '/* testPublicFunctionName */',
                'testType'    => 'T_STRING',
                'testContent' => 'public',
            ],
            'set as parameter type'      => [
                'testMarker'  => '/* testSetParameterType */',
                'testType'    => 'T_STRING',
                'testContent' => 'Set',
            ],

            // Live coding.
            'live coding'                => [
                'testMarker'  => '/* testLiveCoding */',
                'testType'    => 'T_PRIVATE',
                'testContent' => 'private',
            ],
        ];

    }//end dataNotAsymmetricVisibility()


}//end class
