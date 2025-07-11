<?php
/**
 * Test the Ruleset::registerSniffs() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Test the Ruleset::registerSniffs() method.
 *
 * @covers \PHP_CodeSniffer\Ruleset::registerSniffs
 */
final class RegisterSniffsTest extends TestCase
{

    /**
     * The Ruleset object.
     *
     * @var \PHP_CodeSniffer\Ruleset
     */
    private static $ruleset;

    /**
     * Original value of the $sniffs property on the Ruleset.
     *
     * @var array<string, \PHP_CodeSniffer\Sniffs\Sniff>
     */
    private static $originalSniffs = [];

    /**
     * List of Standards dir relative sniff files loaded for the PSR1 standard.
     *
     * @var array<string>
     */
    private static $psr1SniffFiles = [
        'Generic/Sniffs/Files/ByteOrderMarkSniff.php',
        'Generic/Sniffs/NamingConventions/UpperCaseConstantNameSniff.php',
        'Generic/Sniffs/PHP/DisallowAlternativePHPTagsSniff.php',
        'Generic/Sniffs/PHP/DisallowShortOpenTagSniff.php',
        'PSR1/Sniffs/Classes/ClassDeclarationSniff.php',
        'PSR1/Sniffs/Files/SideEffectsSniff.php',
        'PSR1/Sniffs/Methods/CamelCapsMethodNameSniff.php',
        'Squiz/Sniffs/Classes/ValidClassNameSniff.php',
    ];

    /**
     * Absolute paths to the sniff files loaded for the PSR1 standard.
     *
     * @var array<string>
     */
    private static $psr1SniffAbsolutePaths = [];


    /**
     * Initialize the config and ruleset objects which will be used for some of these tests.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function initializeConfigAndRuleset()
    {
        // Set up the ruleset.
        $config        = new ConfigDouble(['--standard=PSR1']);
        self::$ruleset = new Ruleset($config);

        // Remember the original value of the Ruleset::$sniff property as the tests adjust it.
        self::$originalSniffs = self::$ruleset->sniffs;

        // Sort the value to make the tests stable as different OSes will read directories
        // in a different order and the order is not relevant for these tests. Just the values.
        ksort(self::$originalSniffs);

        // Update the sniff file list.
        $standardsDir  = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR;
        $standardsDir .= 'src'.DIRECTORY_SEPARATOR.'Standards'.DIRECTORY_SEPARATOR;

        self::$psr1SniffAbsolutePaths = self::relativeToAbsoluteSniffFiles($standardsDir, self::$psr1SniffFiles);

    }//end initializeConfigAndRuleset()


    /**
     * Convert relative paths to absolute paths and ensure the paths use the correct OS-specific directory separator.
     *
     * @param string        $baseDir       Directory to which these paths are relative to. Including trailing slash.
     * @param array<string> $relativePaths Relative paths.
     *
     * @return array<string>
     */
    public static function relativeToAbsoluteSniffFiles($baseDir, $relativePaths)
    {
        $fileList = [];
        foreach ($relativePaths as $sniffName) {
            $sniffFile  = str_replace('/', DIRECTORY_SEPARATOR, $sniffName);
            $sniffFile  = $baseDir.$sniffFile;
            $fileList[] = $sniffFile;
        }

        return $fileList;

    }//end relativeToAbsoluteSniffFiles()


    /**
     * Clear out the Ruleset::$sniffs property.
     *
     * @before
     *
     * @return void
     */
    protected function clearOutSniffs()
    {
        // Clear out the Ruleset::$sniffs property.
        self::$ruleset->sniffs = [];

    }//end clearOutSniffs()


    /**
     * Test that registering sniffs works as expected (simple base test case).
     *
     * @return void
     */
    public function testRegisteredSniffsShouldBeTheSame()
    {
        self::$ruleset->registerSniffs(self::$psr1SniffAbsolutePaths, [], []);

        // Make sure the same sniff list was recreated (but without the objects having been created yet).
        $this->assertSame(array_keys(self::$originalSniffs), array_keys(self::$ruleset->sniffs));
        $this->assertSame(array_keys(self::$originalSniffs), array_values(self::$ruleset->sniffs));

    }//end testRegisteredSniffsShouldBeTheSame()


    /**
     * Test that if only specific sniffs are requested, only those are registered.
     *
     * {@internal Can't test this via the CLI arguments due to some code in the Ruleset class
     * related to sniff tests.}
     *
     * @return void
     */
    public function testRegisteredSniffsWithRestrictions()
    {
        $restrictions = [
            'psr1\\sniffs\\classes\\classdeclarationsniff'    => true,
            'psr1\\sniffs\\files\\sideeffectssniff'           => true,
            'psr1\\sniffs\\methods\\camelcapsmethodnamesniff' => true,
        ];

        $expected = [
            'PHP_CodeSniffer\\Standards\\PSR1\\Sniffs\\Classes\\ClassDeclarationSniff',
            'PHP_CodeSniffer\\Standards\\PSR1\\Sniffs\\Files\\SideEffectsSniff',
            'PHP_CodeSniffer\\Standards\\PSR1\\Sniffs\\Methods\\CamelCapsMethodNameSniff',
        ];

        self::$ruleset->registerSniffs(self::$psr1SniffAbsolutePaths, $restrictions, []);

        $this->assertSame($expected, array_keys(self::$ruleset->sniffs));

    }//end testRegisteredSniffsWithRestrictions()


    /**
     * Test that sniffs excluded via the CLI are not registered.
     *
     * @return void
     */
    public function testRegisteredSniffsWithExclusions()
    {
        // Set up the ruleset.
        $args    = [
            '--standard=PSR1',
            '--exclude=PSR1.Classes.ClassDeclaration,PSR1.Files.SideEffects,PSR1.Methods.CamelCapsMethodName',
        ];
        $config  = new ConfigDouble($args);
        $ruleset = new Ruleset($config);

        $expected = [
            'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\Files\\ByteOrderMarkSniff',
            'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\NamingConventions\\UpperCaseConstantNameSniff',
            'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\PHP\\DisallowAlternativePHPTagsSniff',
            'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\PHP\\DisallowShortOpenTagSniff',
            'PHP_CodeSniffer\\Standards\\Squiz\\Sniffs\\Classes\\ValidClassNameSniff',
        ];

        $actual = array_keys($ruleset->sniffs);
        sort($actual);

        $this->assertSame($expected, $actual);

    }//end testRegisteredSniffsWithExclusions()


    /**
     * Test combining requesting specific sniffs and excluding a subset of those.
     *
     * @return void
     */
    public function testRegisteredSniffsBothRestrictionsAndExclusions()
    {
        $restrictions = [
            'generic\\sniffs\\namingconventions\\uppercaseconstantnamesniff' => true,
            'generic\\sniffs\\php\\disallowalternativephptagssniff'          => true,
            'generic\\sniffs\\php\\disallowshortopentagsniff'                => true,
            'psr1\\sniffs\\classes\\classdeclarationsniff'                   => true,
            'squiz\\sniffs\\classes\\validclassnamesniff'                    => true,
        ];

        $exclusions = [
            'squiz\\sniffs\\classes\\validclassnamesniff'                    => true,
            'generic\\sniffs\\php\\disallowalternativephptagssniff'          => true,
            'generic\\sniffs\\namingconventions\\uppercaseconstantnamesniff' => true,
        ];

        $expected = [
            'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\PHP\\DisallowShortOpenTagSniff',
            'PHP_CodeSniffer\\Standards\\PSR1\\Sniffs\\Classes\\ClassDeclarationSniff',
        ];

        self::$ruleset->registerSniffs(self::$psr1SniffAbsolutePaths, $restrictions, $exclusions);

        $this->assertSame($expected, array_keys(self::$ruleset->sniffs));

    }//end testRegisteredSniffsBothRestrictionsAndExclusions()


    /**
     * Verify that abstract sniffs are filtered out and not registered.
     *
     * @return void
     */
    public function testRegisterSniffsFiltersOutAbstractClasses()
    {
        $extraPathsBaseDir = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR;
        $extraPaths        = [
            'DirectoryExpansion/.hiddenAbove/src/MyStandard/Sniffs/AbstractSniff.php',
            'DirectoryExpansion/.hiddenAbove/src/MyStandard/Sniffs/CategoryB/AnotherAbstractSniff.php',
        ];
        $extraPaths        = self::relativeToAbsoluteSniffFiles($extraPathsBaseDir, $extraPaths);

        $fileList = self::$psr1SniffAbsolutePaths;
        foreach ($extraPaths as $path) {
            $fileList[] = $path;
        }

        self::$ruleset->registerSniffs($fileList, [], []);

        // Make sure the same sniff list was recreated (but without the objects having been created yet).
        $this->assertSame(array_keys(self::$originalSniffs), array_keys(self::$ruleset->sniffs));
        $this->assertSame(array_keys(self::$originalSniffs), array_values(self::$ruleset->sniffs));

    }//end testRegisterSniffsFiltersOutAbstractClasses()


    /**
     * Test that sniff files not in a "/Sniffs/" directory are filtered out and not registered.
     *
     * @return void
     */
    public function testRegisteredSniffsFiltersOutFilePathsWithoutSniffsDir()
    {
        $extraPathsBaseDir = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR;
        $extraPaths        = [
            'DirectoryExpansion/.hiddenAbove/src/MyStandard/Utils/NotInSniffsDirSniff.php',
            'DirectoryExpansion/.hiddenAbove/src/MyStandard/Utils/SubDir/NotInSniffsDirSniff.php',
        ];
        $extraPaths        = self::relativeToAbsoluteSniffFiles($extraPathsBaseDir, $extraPaths);

        $fileList = self::$psr1SniffAbsolutePaths;
        foreach ($extraPaths as $path) {
            $fileList[] = $path;
        }

        self::$ruleset->registerSniffs($fileList, [], []);

        // Make sure the same sniff list was recreated (but without the objects having been created yet).
        $this->assertSame(array_keys(self::$originalSniffs), array_keys(self::$ruleset->sniffs));
        $this->assertSame(array_keys(self::$originalSniffs), array_values(self::$ruleset->sniffs));

    }//end testRegisteredSniffsFiltersOutFilePathsWithoutSniffsDir()


}//end class
