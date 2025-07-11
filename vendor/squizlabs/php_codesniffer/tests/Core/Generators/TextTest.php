<?php
/**
 * Tests the Text documentation generation.
 *
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Generators;

use PHP_CodeSniffer\Generators\Text;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Test the Text documentation generation.
 *
 * @covers \PHP_CodeSniffer\Generators\Text
 * @group  Windows
 */
final class TextTest extends TestCase
{


    /**
     * Test the generated docs.
     *
     * @param string $standard       The standard to use for the test.
     * @param string $pathToExpected Path to a file containing the expected function output.
     *
     * @dataProvider dataDocs
     *
     * @return void
     */
    public function testDocs($standard, $pathToExpected)
    {
        // Set up the ruleset.
        $config  = new ConfigDouble(["--standard=$standard"]);
        $ruleset = new Ruleset($config);

        $expected = file_get_contents($pathToExpected);
        $this->assertNotFalse($expected, 'Output expectation file could not be found');

        // Make the test OS independent.
        $expected = str_replace("\n", PHP_EOL, $expected);
        $this->expectOutputString($expected);

        $generator = new Text($ruleset);
        $generator->generate();

    }//end testDocs()


    /**
     * Data provider.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataDocs()
    {
        return [
            'Standard without docs'            => [
                'standard'       => __DIR__.'/NoDocsTest.xml',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputEmpty.txt',
            ],
            'Standard with one doc file'       => [
                'standard'       => __DIR__.'/OneDocTest.xml',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputOneDoc.txt',
            ],
            'Standard with multiple doc files' => [
                'standard'       => __DIR__.'/StructureDocsTest.xml',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputStructureDocs.txt',
            ],
        ];

    }//end dataDocs()


    /**
     * Test the generated docs for the handling of specific parts of the documentation.
     *
     * @param string $sniffs         The specific fixture sniffs to verify the docs for.
     * @param string $pathToExpected Path to a file containing the expected function output.
     *
     * @dataProvider dataDocSpecifics
     *
     * @return void
     */
    public function testDocSpecifics($sniffs, $pathToExpected)
    {
        // Set up the ruleset.
        $standard = __DIR__.'/AllValidDocsTest.xml';
        $config   = new ConfigDouble(["--standard=$standard", "--sniffs=$sniffs"]);
        $ruleset  = new Ruleset($config);

        // In tests, the `--sniffs` setting doesn't work out of the box.
        $sniffParts = explode('.', $sniffs);
        $sniffFile  = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.$sniffParts[0].DIRECTORY_SEPARATOR;
        $sniffFile .= 'Sniffs'.DIRECTORY_SEPARATOR.$sniffParts[1].DIRECTORY_SEPARATOR.$sniffParts[2].'Sniff.php';

        $sniffParts   = array_map('strtolower', $sniffParts);
        $sniffName    = $sniffParts[0].'\sniffs\\'.$sniffParts[1].'\\'.$sniffParts[2].'sniff';
        $restrictions = [$sniffName => true];
        $ruleset->registerSniffs([$sniffFile], $restrictions, []);

        $expected = file_get_contents($pathToExpected);
        $this->assertNotFalse($expected, 'Output expectation file could not be found');

        // Make the test OS independent.
        $expected = str_replace("\n", PHP_EOL, $expected);
        $this->expectOutputString($expected);

        $generator = new Text($ruleset);
        $generator->generate();

    }//end testDocSpecifics()


    /**
     * Data provider.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataDocSpecifics()
    {
        return [
            'Documentation title: case'                         => [
                'sniffs'         => 'StandardWithDocs.Content.DocumentationTitleCase',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputDocumentationTitleCase.txt',
            ],
            'Documentation title: length'                       => [
                'sniffs'         => 'StandardWithDocs.Content.DocumentationTitleLength',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputDocumentationTitleLength.txt',
            ],
            'Documentation title: fallback to file name'        => [
                'sniffs'         => 'StandardWithDocs.Content.DocumentationTitlePCREFallback',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputDocumentationTitlePCREFallback.txt',
            ],
            'Standard Element: blank line handling'             => [
                'sniffs'         => 'StandardWithDocs.Content.StandardBlankLines',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputStandardBlankLines.txt',
            ],
            'Standard Element: encoding of special characters'  => [
                'sniffs'         => 'StandardWithDocs.Content.StandardEncoding',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputStandardEncoding.txt',
            ],
            'Standard Element: indent handling'                 => [
                'sniffs'         => 'StandardWithDocs.Content.StandardIndent',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputStandardIndent.txt',
            ],
            'Standard Element: line wrapping'                   => [
                'sniffs'         => 'StandardWithDocs.Content.StandardLineWrapping',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputStandardLineWrapping.txt',
            ],
            'Code Title: line wrapping'                         => [
                'sniffs'         => 'StandardWithDocs.Content.CodeTitleLineWrapping',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputCodeTitleLineWrapping.txt',
            ],
            'Code Title: whitespace handling'                   => [
                'sniffs'         => 'StandardWithDocs.Content.CodeTitleWhitespace',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputCodeTitleWhitespace.txt',
            ],
            'Code Comparison: blank line handling'              => [
                'sniffs'         => 'StandardWithDocs.Content.CodeComparisonBlankLines',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputCodeComparisonBlankLines.txt',
            ],
            'Code Comparison: different block lengths'          => [
                'sniffs'         => 'StandardWithDocs.Content.CodeComparisonBlockLength',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputCodeComparisonBlockLength.txt',
            ],
            'Code Comparison: encoding of special characters'   => [
                'sniffs'         => 'StandardWithDocs.Content.CodeComparisonEncoding',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputCodeComparisonEncoding.txt',
            ],
            'Code Comparison: line length handling'             => [
                'sniffs'         => 'StandardWithDocs.Content.CodeComparisonLineLength',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputCodeComparisonLineLength.txt',
            ],
            'Unsupported: <code> element at the wrong level'    => [
                'sniffs'         => 'StandardWithDocs.Unsupported.ElementAtWrongLevel',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputEmpty.txt',
            ],
            'Unsupported: one correct elm, one at wrong level'  => [
                'sniffs'         => 'StandardWithDocs.Unsupported.OneElmAtWrongLevel',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputUnsupportedOneElmAtWrongLevel.txt',
            ],
            'Unsupported: superfluous code element'             => [
                'sniffs'         => 'StandardWithDocs.Unsupported.SuperfluousCodeElement',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputUnsupportedSuperfluousCodeElement.txt',
            ],
            'Unsupported: unknown element'                      => [
                'sniffs'         => 'StandardWithDocs.Unsupported.UnknownElement',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputEmpty.txt',
            ],
            'Invalid: code comparison mismatched code elms'     => [
                'sniffs'         => 'StandardWithDocs.Invalid.CodeComparisonMismatchedCodeElms',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputInvalidCodeComparisonMismatchedCodeElms.txt',
            ],
            'Invalid: code comparison only has one code elm'    => [
                'sniffs'         => 'StandardWithDocs.Invalid.CodeComparisonMissingCodeElm',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputInvalidCodeComparisonMissingCodeElm.txt',
            ],
            'Invalid: code elements have no content'            => [
                'sniffs'         => 'StandardWithDocs.Invalid.CodeComparisonNoCode',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputInvalidCodeComparisonNoCode.txt',
            ],
            'Invalid: code comparison element has no content'   => [
                'sniffs'         => 'StandardWithDocs.Invalid.CodeComparisonNoContent',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputInvalidCodeComparisonNoContent.txt',
            ],
            'Invalid: code comparison two code elms, one empty' => [
                'sniffs'         => 'StandardWithDocs.Invalid.CodeComparisonOneEmptyCodeElm',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputInvalidCodeComparisonOneEmptyCodeElm.txt',
            ],
            'Invalid: code comparison two empty code elms'      => [
                'sniffs'         => 'StandardWithDocs.Invalid.CodeComparisonTwoEmptyCodeElms',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputInvalidCodeComparisonTwoEmptyCodeElms.txt',
            ],
            'Invalid: code title attributes are empty'          => [
                'sniffs'         => 'StandardWithDocs.Invalid.CodeTitleEmpty',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputInvalidCodeTitleEmpty.txt',
            ],
            'Invalid: code title attributes missing'            => [
                'sniffs'         => 'StandardWithDocs.Invalid.CodeTitleMissing',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputInvalidCodeTitleMissing.txt',
            ],
            'Invalid: documentation title attribute is empty'   => [
                'sniffs'         => 'StandardWithDocs.Invalid.DocumentationTitleEmpty',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputInvalidDocumentationTitleEmpty.txt',
            ],
            'Invalid: documentation title attribute missing'    => [
                'sniffs'         => 'StandardWithDocs.Invalid.DocumentationTitleMissing',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputInvalidDocumentationTitleMissing.txt',
            ],
            'Invalid: standard element has no content'          => [
                'sniffs'         => 'StandardWithDocs.Invalid.StandardNoContent',
                'pathToExpected' => __DIR__.'/Expectations/ExpectedOutputInvalidStandardNoContent.txt',
            ],
        ];

    }//end dataDocSpecifics()


}//end class
