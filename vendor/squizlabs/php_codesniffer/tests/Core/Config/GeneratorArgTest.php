<?php
/**
 * Tests for the \PHP_CodeSniffer\Config --generator argument.
 *
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Config;

use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHP_CodeSniffer\Config --generator argument.
 *
 * @covers \PHP_CodeSniffer\Config::processLongArgument
 */
final class GeneratorArgTest extends TestCase
{


    /**
     * Skip these tests when in CBF mode.
     *
     * @before
     *
     * @return void
     */
    protected function maybeSkipTests()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('The `--generator` CLI flag is only supported for the `phpcs` command');
        }

    }//end maybeSkipTests()


    /**
     * Ensure that the generator property is set when the parameter is passed a valid value.
     *
     * @param string $argumentValue         Generator name passed on the command line.
     * @param string $expectedPropertyValue Expected value of the generator property.
     *
     * @dataProvider dataValidGeneratorNames
     *
     * @return void
     */
    public function testValidGenerators($argumentValue, $expectedPropertyValue)
    {
        $config = new ConfigDouble(["--generator=$argumentValue"]);

        $this->assertSame($expectedPropertyValue, $config->generator);

    }//end testValidGenerators()


    /**
     * Data provider for testValidGenerators().
     *
     * @see self::testValidGenerators()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataValidGeneratorNames()
    {
        return [
            'Text generator passed'            => [
                'argumentValue'         => 'Text',
                'expectedPropertyValue' => 'Text',
            ],
            'HTML generator passed'            => [
                'argumentValue'         => 'HTML',
                'expectedPropertyValue' => 'HTML',
            ],
            'Markdown generator passed'        => [
                'argumentValue'         => 'Markdown',
                'expectedPropertyValue' => 'Markdown',
            ],
            'Uppercase Text generator passed'  => [
                'argumentValue'         => 'TEXT',
                'expectedPropertyValue' => 'Text',
            ],
            'Mixed case Text generator passed' => [
                'argumentValue'         => 'tEXt',
                'expectedPropertyValue' => 'Text',
            ],
            'Lowercase HTML generator passed'  => [
                'argumentValue'         => 'html',
                'expectedPropertyValue' => 'HTML',
            ],
        ];

    }//end dataValidGeneratorNames()


    /**
     * Ensure that only the first argument is processed and others are ignored.
     *
     * @return void
     */
    public function testOnlySetOnce()
    {
        $config = new ConfigDouble(
            [
                '--generator=Text',
                '--generator=HTML',
                '--generator=InvalidGenerator',
            ]
        );

        $this->assertSame('Text', $config->generator);

    }//end testOnlySetOnce()


    /**
     * Ensure that an exception is thrown for an invalid generator.
     *
     * @param string $generatorName Generator name.
     *
     * @dataProvider dataInvalidGeneratorNames
     *
     * @return void
     */
    public function testInvalidGenerator($generatorName)
    {
        $exception = 'PHP_CodeSniffer\Exceptions\DeepExitException';
        $message   = 'ERROR: "'.$generatorName.'" is not a valid generator. The following generators are supported: Text, HTML and Markdown.';

        if (method_exists($this, 'expectException') === true) {
            // PHPUnit 5+.
            $this->expectException($exception);
            $this->expectExceptionMessage($message);
        } else {
            // PHPUnit 4.
            $this->setExpectedException($exception, $message);
        }

        new ConfigDouble(["--generator={$generatorName}"]);

    }//end testInvalidGenerator()


    /**
     * Data provider for testInvalidGenerator().
     *
     * @see self::testInvalidGenerator()
     *
     * @return array<array<string>>
     */
    public static function dataInvalidGeneratorNames()
    {
        return [
            ['InvalidGenerator'],
            ['Text,HTML'],
            [''],
        ];

    }//end dataInvalidGeneratorNames()


}//end class
