<?php
/**
 * Base class to use when testing parts of the tokenizer.
 *
 * This is a near duplicate of the AbstractMethodUnitTest class, with the
 * difference being that it allows for recording code coverage for tokenizer tests.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2018-2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Tokenizer;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

abstract class AbstractTokenizerTestCase extends TestCase
{

    /**
     * The file extension of the test case file (without leading dot).
     *
     * This allows child classes to overrule the default `inc` with, for instance,
     * `js` or `css` when applicable.
     *
     * @var string
     */
    protected $fileExtension = 'inc';

    /**
     * The tab width setting to use when tokenizing the file.
     *
     * This allows for test case files to use a different tab width than the default.
     *
     * @var integer
     */
    protected $tabWidth = 4;

    /**
     * The \PHP_CodeSniffer\Files\File object containing the parsed contents of the test case file.
     *
     * @var \PHP_CodeSniffer\Files\File
     */
    protected $phpcsFile;


    /**
     * Initialize & tokenize \PHP_CodeSniffer\Files\File with code from the test case file.
     *
     * The test case file for a unit test class has to be in the same directory
     * directory and use the same file name as the test class, using the .inc extension.
     *
     * @before
     *
     * @return void
     */
    protected function initializeFile()
    {
        if (isset($this->phpcsFile) === false) {
            $config = new ConfigDouble();
            // Also set a tab-width to enable testing tab-replaced vs `orig_content`.
            $config->tabWidth = $this->tabWidth;

            $ruleset = new Ruleset($config);

            // Default to a file with the same name as the test class. Extension is property based.
            $relativeCN     = str_replace(__NAMESPACE__, '', get_called_class());
            $relativePath   = str_replace('\\', DIRECTORY_SEPARATOR, $relativeCN);
            $pathToTestFile = realpath(__DIR__).$relativePath.'.'.$this->fileExtension;

            // Make sure the file gets parsed correctly based on the file type.
            $contents  = 'phpcs_input_file: '.$pathToTestFile.PHP_EOL;
            $contents .= file_get_contents($pathToTestFile);

            $this->phpcsFile = new DummyFile($contents, $ruleset, $config);
            $this->phpcsFile->process();
        }

    }//end initializeFile()


    /**
     * Get the token pointer for a target token based on a specific comment found on the line before.
     *
     * Note: the test delimiter comment MUST start with "/* test" to allow this function to
     * distinguish between comments used *in* a test and test delimiters.
     *
     * @param string           $commentString The delimiter comment to look for.
     * @param int|string|array $tokenType     The type of token(s) to look for.
     * @param string           $tokenContent  Optional. The token content for the target token.
     *
     * @return int
     */
    protected function getTargetToken($commentString, $tokenType, $tokenContent=null)
    {
        return AbstractMethodUnitTest::getTargetTokenFromFile($this->phpcsFile, $commentString, $tokenType, $tokenContent);

    }//end getTargetToken()


    /**
     * Clear the static "resolved tokens" cache property on the Tokenizer\PHP class.
     *
     * This method should be used selectively by tests to ensure the code under test is actually hit
     * by the test testing the code.
     *
     * @return void
     */
    public static function clearResolvedTokensCache()
    {
        $property = new ReflectionProperty('PHP_CodeSniffer\Tokenizers\PHP', 'resolveTokenCache');
        $property->setAccessible(true);
        $property->setValue(null, []);
        $property->setAccessible(false);

    }//end clearResolvedTokensCache()


}//end class
