<?php
/**
 * Tests for the \PHP_CodeSniffer\Util\Tokens::getHighestWeightedToken() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Util\Tokens;

use PHP_CodeSniffer\Util\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHP_CodeSniffer\Util\Tokens::getHighestWeightedToken() method.
 *
 * @covers \PHP_CodeSniffer\Util\Tokens::getHighestWeightedToken
 */
final class GetHighestWeightedTokenTest extends TestCase
{


    /**
     * Test the method.
     *
     * @param array<int|string> $tokens   The tokens to find the heighest weighted one.
     * @param int|false         $expected The expected function return value.
     *
     * @dataProvider dataGetHighestWeightedToken
     *
     * @return void
     */
    public function testGetHighestWeightedToken($tokens, $expected)
    {
        $this->assertSame($expected, Tokens::getHighestWeightedToken($tokens));

    }//end testGetHighestWeightedToken()


    /**
     * Data provider.
     *
     * @return array<string, array<string, int|false|array<int|string>>>
     */
    public static function dataGetHighestWeightedToken()
    {
        $data = [
            'Array of non-tokens passed, returns first'                              => [
                'tokens'   => [
                    PHP_SAPI,
                    PHP_MAJOR_VERSION,
                    PHP_OS,
                ],
                'expected' => PHP_SAPI,
            ],
            'No weightings available for any of the selected tokens, first one wins' => [
                'tokens'   => [
                    T_VARIABLE,
                    T_STRING,
                    T_EXTENDS,
                    T_IMPLEMENTS,
                ],
                'expected' => T_VARIABLE,
            ],
            'single token always returns that token'                                 => [
                'tokens'   => [T_VARIABLE],
                'expected' => T_VARIABLE,
            ],
            'Unknown and known token, known token wins'                              => [
                'tokens'   => [
                    T_VARIABLE,
                    T_SELF,
                ],
                'expected' => T_SELF,
            ],
            'Known and unknown token, known token wins'                              => [
                'tokens'   => [
                    T_CLOSURE,
                    T_STRING,
                ],
                'expected' => T_CLOSURE,
            ],
            'Two tokens with equal weights passed, first one wins'                   => [
                'tokens'   => [
                    T_CLOSURE,
                    T_FUNCTION,
                ],
                'expected' => T_CLOSURE,
            ],
            'Five tokens with equal weights passed, first one wins'                  => [
                'tokens'   => [
                    T_NAMESPACE,
                    T_TRAIT,
                    T_ENUM,
                    T_CLASS,
                    T_INTERFACE,
                ],
                'expected' => T_NAMESPACE,
            ],
            'Tokens with different weights passed, heightest (25) wins'              => [
                'tokens'   => [
                    T_BITWISE_OR,
                    T_SELF,
                    T_MUL_EQUAL,
                ],
                'expected' => T_SELF,
            ],
            'Tokens with different weights passed, heightest (50) wins'              => [
                'tokens'   => [
                    T_BITWISE_XOR,
                    T_CATCH,
                    T_SPACESHIP,
                    T_PARENT,
                ],
                'expected' => T_CATCH,
            ],
        ];

        $high100 = [
            T_MULTIPLY,
            T_BITWISE_AND,
            T_SELF,
            T_FOREACH,
            T_CLOSURE,
        ];
        $data['Tokens with different weights passed, ordered low-high, heightest (100) wins'] = [
            'tokens'   => $high100,
            'expected' => T_CLOSURE,
        ];

        shuffle($high100);
        $data['Tokens with different weights passed, order random, heightest (100) wins'] = [
            'tokens'   => $high100,
            'expected' => T_CLOSURE,
        ];

        $high1000 = [
            T_ENUM,
            T_FUNCTION,
            T_ELSEIF,
            T_PARENT,
            T_BITWISE_OR,
            T_MODULUS,
        ];
        $data['Tokens with different weights passed, ordered low-high, heightest (1000) wins'] = [
            'tokens'   => $high1000,
            'expected' => T_ENUM,
        ];

        shuffle($high1000);
        $data['Tokens with different weights passed, order random, heightest (1000) wins'] = [
            'tokens'   => $high1000,
            'expected' => T_ENUM,
        ];

        return $data;

    }//end dataGetHighestWeightedToken()


}//end class
