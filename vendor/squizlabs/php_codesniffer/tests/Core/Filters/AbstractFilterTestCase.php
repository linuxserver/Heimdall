<?php
/**
 * Abstract Testcase class for testing Filters.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2023 PHPCSStandards Contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Filters;

use PHP_CodeSniffer\Filters\Filter;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;
use RecursiveIteratorIterator;

/**
 * Base functionality and utilities for testing Filter classes.
 */
abstract class AbstractFilterTestCase extends TestCase
{

    /**
     * The Config object.
     *
     * @var \PHP_CodeSniffer\Config
     */
    protected static $config;

    /**
     * The Ruleset object.
     *
     * @var \PHP_CodeSniffer\Ruleset
     */
    protected static $ruleset;


    /**
     * Initialize the config and ruleset objects.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function initializeConfigAndRuleset()
    {
        self::$config  = new ConfigDouble(['--extensions=php,inc/php,js,css']);
        self::$ruleset = new Ruleset(self::$config);

    }//end initializeConfigAndRuleset()


    /**
     * Helper method to retrieve a mock object for a Filter class.
     *
     * The `setMethods()` method was silently deprecated in PHPUnit 9 and removed in PHPUnit 10.
     *
     * Note: direct access to the `getMockBuilder()` method is soft deprecated as of PHPUnit 10,
     * and expected to be hard deprecated in PHPUnit 11 and removed in PHPUnit 12.
     * Dealing with that is something for a later iteration of the test suite.
     *
     * @param string             $className       Fully qualified name of the class under test.
     * @param array<mixed>       $constructorArgs Optional. Array of parameters to pass to the class constructor.
     * @param array<string>|null $methodsToMock   Optional. The methods to mock in the class under test.
     *                                            Needed for PHPUnit cross-version support as PHPUnit 4.x does
     *                                            not have a `setMethodsExcept()` method yet.
     *                                            If not passed, no methods will be replaced.
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockedClass($className, array $constructorArgs=[], $methodsToMock=null)
    {
        $mockedObj = $this->getMockBuilder($className);

        if (method_exists($mockedObj, 'onlyMethods') === true) {
            // PHPUnit 8+.
            if (is_array($methodsToMock) === true) {
                return $mockedObj
                    ->setConstructorArgs($constructorArgs)
                    ->onlyMethods($methodsToMock)
                    ->getMock();
            }

            return $mockedObj->getMock()
                ->setConstructorArgs($constructorArgs);
        }

        // PHPUnit < 8.
        return $mockedObj
            ->setConstructorArgs($constructorArgs)
            ->setMethods($methodsToMock)
            ->getMock();

    }//end getMockedClass()


    /**
     * Retrieve an array of files which were accepted by a filter.
     *
     * @param \PHP_CodeSniffer\Filters\Filter $filter The Filter object under test.
     *
     * @return array<string>
     */
    protected function getFilteredResultsAsArray(Filter $filter)
    {
        $iterator = new RecursiveIteratorIterator($filter);
        $files    = [];
        foreach ($iterator as $file) {
            $files[] = $file;
        }

        return $files;

    }//end getFilteredResultsAsArray()


    /**
     * Retrieve the basedir to use for tests using the `getFakeFileList()` method.
     *
     * @return string
     */
    protected static function getBaseDir()
    {
        return dirname(dirname(dirname(__DIR__)));

    }//end getBaseDir()


    /**
     * Retrieve a file list containing a range of paths for testing purposes.
     *
     * This list **must** contain files which exist in this project (well, except for some which don't exist
     * purely for testing purposes), as `realpath()` is used in the logic under test and `realpath()` will
     * return `false` for any non-existent files, which will automatically filter them out before
     * we get to the code under test.
     *
     * Note this list does not include `.` and `..` as \PHP_CodeSniffer\Files\FileList uses `SKIP_DOTS`.
     *
     * @return array<string>
     */
    protected static function getFakeFileList()
    {
        $basedir = self::getBaseDir();
        return [
            $basedir.'/.gitignore',
            $basedir.'/.yamllint.yml',
            $basedir.'/phpcs.xml',
            $basedir.'/phpcs.xml.dist',
            $basedir.'/autoload.php',
            $basedir.'/bin',
            $basedir.'/bin/phpcs',
            $basedir.'/bin/phpcs.bat',
            $basedir.'/scripts',
            $basedir.'/scripts/build-phar.php',
            $basedir.'/src',
            $basedir.'/src/WillNotExist.php',
            $basedir.'/src/WillNotExist.bak',
            $basedir.'/src/WillNotExist.orig',
            $basedir.'/src/Ruleset.php',
            $basedir.'/src/Generators',
            $basedir.'/src/Generators/Markdown.php',
            $basedir.'/src/Standards',
            $basedir.'/src/Standards/Generic',
            $basedir.'/src/Standards/Generic/Docs',
            $basedir.'/src/Standards/Generic/Docs/Classes',
            $basedir.'/src/Standards/Generic/Docs/Classes/DuplicateClassNameStandard.xml',
            $basedir.'/src/Standards/Generic/Sniffs',
            $basedir.'/src/Standards/Generic/Sniffs/Classes',
            $basedir.'/src/Standards/Generic/Sniffs/Classes/DuplicateClassNameSniff.php',
            $basedir.'/src/Standards/Generic/Tests',
            $basedir.'/src/Standards/Generic/Tests/Classes',
            $basedir.'/src/Standards/Generic/Tests/Classes/DuplicateClassNameUnitTest.1.inc',
            // Will rarely exist when running the tests.
            $basedir.'/src/Standards/Generic/Tests/Classes/DuplicateClassNameUnitTest.1.inc.bak',
            $basedir.'/src/Standards/Generic/Tests/Classes/DuplicateClassNameUnitTest.2.inc',
            $basedir.'/src/Standards/Generic/Tests/Classes/DuplicateClassNameUnitTest.php',
            $basedir.'/src/Standards/Squiz',
            $basedir.'/src/Standards/Squiz/Docs',
            $basedir.'/src/Standards/Squiz/Docs/WhiteSpace',
            $basedir.'/src/Standards/Squiz/Docs/WhiteSpace/SemicolonSpacingStandard.xml',
            $basedir.'/src/Standards/Squiz/Sniffs',
            $basedir.'/src/Standards/Squiz/Sniffs/WhiteSpace',
            $basedir.'/src/Standards/Squiz/Sniffs/WhiteSpace/OperatorSpacingSniff.php',
            $basedir.'/src/Standards/Squiz/Tests',
            $basedir.'/src/Standards/Squiz/Tests/WhiteSpace',
            $basedir.'/src/Standards/Squiz/Tests/WhiteSpace/OperatorSpacingUnitTest.inc',
            $basedir.'/src/Standards/Squiz/Tests/WhiteSpace/OperatorSpacingUnitTest.inc.fixed',
            $basedir.'/src/Standards/Squiz/Tests/WhiteSpace/OperatorSpacingUnitTest.js',
            $basedir.'/src/Standards/Squiz/Tests/WhiteSpace/OperatorSpacingUnitTest.js.fixed',
            $basedir.'/src/Standards/Squiz/Tests/WhiteSpace/OperatorSpacingUnitTest.php',
        ];

    }//end getFakeFileList()


    /**
     * Translate Linux paths to Windows paths, when necessary.
     *
     * These type of tests should be able to run and pass on both *nix as well as Windows
     * based dev systems. This method is a helper to allow for this.
     *
     * @param array<string|array> $paths A single or multi-dimensional array containing
     *                                   file paths.
     *
     * @return array<string|array>
     */
    protected static function mapPathsToRuntimeOs(array $paths)
    {
        if (DIRECTORY_SEPARATOR !== '\\') {
            return $paths;
        }

        foreach ($paths as $key => $value) {
            if (is_string($value) === true) {
                $paths[$key] = strtr($value, '/', '\\\\');
            } else if (is_array($value) === true) {
                $paths[$key] = self::mapPathsToRuntimeOs($value);
            }
        }

        return $paths;

    }//end mapPathsToRuntimeOs()


}//end class
