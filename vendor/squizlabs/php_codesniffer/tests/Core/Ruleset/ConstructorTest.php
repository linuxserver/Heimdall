<?php
/**
 * Test the Ruleset::__construct() method.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Autoload;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Core\Ruleset\AbstractRulesetTestCase;

/**
 * Test various aspects of the Ruleset::__construct() method not covered via other tests.
 *
 * @covers \PHP_CodeSniffer\Ruleset::__construct
 */
final class ConstructorTest extends AbstractRulesetTestCase
{


    /**
     * Test setting the ruleset name.
     *
     * @param array<string> $cliArgs  The CLI args to pass to the Config.
     * @param string        $expected The expected set ruleset name.
     *
     * @dataProvider dataHandlingStandardsPassedViaCLI
     *
     * @return void
     */
    public function testHandlingStandardsPassedViaCLI($cliArgs, $expected)
    {
        $config  = new ConfigDouble($cliArgs);
        $ruleset = new Ruleset($config);

        $this->assertSame($expected, $ruleset->name);

    }//end testHandlingStandardsPassedViaCLI()


    /**
     * Data provider.
     *
     * @see testHandlingStandardsPassedViaCLI()
     *
     * @return array<string, array<string, string|array<string>>>
     */
    public static function dataHandlingStandardsPassedViaCLI()
    {
        return [
            'Single standard passed'                     => [
                'cliArgs'  => ['--standard=PSR1'],
                'expected' => 'PSR1',
            ],
            'Multiple standards passed'                  => [
                'cliArgs'  => ['--standard=PSR1,Zend'],
                'expected' => 'PSR1, Zend',
            ],
            'Absolute path to standard directory passed' => [
                'cliArgs'  => [
                    '--standard='.__DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'TestStandard',
                    // Limit this to a valid sniff to prevent running into error messages unrelated to what
                    // is being tested here.
                    '--sniffs=TestStandard.ValidSniffs.RegisterEmptyArray',
                ],
                'expected' => 'TestStandard',
            ],
        ];

    }//end dataHandlingStandardsPassedViaCLI()


    /**
     * Verify that standards are registered with the Autoloader.
     *
     * @param array<string>         $cliArgs  The CLI args to pass to the Config.
     * @param array<string, string> $expected Minimum set of standards expected to be registered with the autoloader.
     *
     * @dataProvider dataStandardsAreRegisteredWithAutoloader
     *
     * @return void
     */
    public function testStandardsAreRegisteredWithAutoloader($cliArgs, $expected)
    {
        $config = new ConfigDouble($cliArgs);
        new Ruleset($config);

        $autoloadPaths = Autoload::getSearchPaths();

        // Note: doing a full comparison of the Autoloader registered standards would make this test unstable
        // as the `CodeSniffer.conf` of the user running the tests could interfer if they have additional
        // external standards registered.
        // Also note that `--runtime-set` is being used to set `installed_paths` to prevent making any changes to
        // the `CodeSniffer.conf` file of the user running the tests.
        foreach ($expected as $path => $namespacedStandardName) {
            $this->assertArrayHasKey($path, $autoloadPaths, "Path $path has not been registered with the autoloader");
            $this->assertSame($namespacedStandardName, $autoloadPaths[$path], 'Expected (namespaced) standard name does not match');
        }

    }//end testStandardsAreRegisteredWithAutoloader()


    /**
     * Data provider.
     *
     * @see testStandardsAreRegisteredWithAutoloader()
     *
     * @return array<string, array<string, array<int|string, string>>>
     */
    public static function dataStandardsAreRegisteredWithAutoloader()
    {
        $basePath     = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Standards'.DIRECTORY_SEPARATOR;
        $defaultPaths = [
            $basePath.'MySource' => 'MySource',
            $basePath.'PEAR'     => 'PEAR',
            $basePath.'PSR1'     => 'PSR1',
            $basePath.'PSR12'    => 'PSR12',
            $basePath.'PSR2'     => 'PSR2',
            $basePath.'Squiz'    => 'Squiz',
            $basePath.'Zend'     => 'Zend',
        ];

        $data = [
            'Default standards' => [
                'cliArgs'  => [
                    '--standard=PSR1',
                    '--runtime-set installed_paths .',
                ],
                'expected' => $defaultPaths,
            ],
        ];

        $extraInstalledPath  = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'DirectoryExpansion';
        $extraInstalledPath .= DIRECTORY_SEPARATOR.'.hiddenAbove'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'MyStandard';
        $data['Additional non-namespaced standard'] = [
            'cliArgs'  => [
                '--standard=MyStandard',
                '--runtime-set',
                'installed_paths',
                $extraInstalledPath,
            ],
            'expected' => ($defaultPaths + [$extraInstalledPath => 'MyStandard']),
        ];

        $extraInstalledPath = __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'TestStandard';
        $data['Additional namespaced standard'] = [
            'cliArgs'  => [
                '--standard=TestStandard',
                '--runtime-set',
                'installed_paths',
                $extraInstalledPath,
                // Limit this to a valid sniff to prevent running into error messages unrelated to what
                // is being tested here.
                '--sniffs=TestStandard.ValidSniffs.RegisterEmptyArray',
            ],
            'expected' => ($defaultPaths + [$extraInstalledPath => 'Fixtures\TestStandard']),
        ];

        return $data;

    }//end dataStandardsAreRegisteredWithAutoloader()


    /**
     * Verify handling of sniff restrictions in combination with the caching setting.
     *
     * @param array<string> $cliArgs  The CLI args to pass to the Config.
     * @param bool          $cache    Whether to turn the cache on or off.
     * @param array<string> $expected Sniffs which are expected to have been registered.
     *
     * @dataProvider dataCachingVersusRestrictions
     *
     * @return void
     */
    public function testCachingVersusRestrictions($cliArgs, $cache, $expected)
    {
        $config = new ConfigDouble($cliArgs);

        // Overrule the cache setting (which is being ignored in the Config when the tests are running).
        $config->cache = $cache;

        $ruleset = new Ruleset($config);

        $actual = array_keys($ruleset->sniffs);
        sort($actual);

        $this->assertSame($expected, $actual);

    }//end testCachingVersusRestrictions()


    /**
     * Data provider.
     *
     * Note: the test cases only use `--exclude` to restrict,
     *
     * @see testCachingVersusRestrictions()
     *
     * @return array<string, array<string, bool|array<string>>>
     */
    public static function dataCachingVersusRestrictions()
    {
        $completeSet = [
            'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\Files\\ByteOrderMarkSniff',
            'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\NamingConventions\\UpperCaseConstantNameSniff',
            'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\PHP\\DisallowAlternativePHPTagsSniff',
            'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\PHP\\DisallowShortOpenTagSniff',
            'PHP_CodeSniffer\\Standards\\PSR1\\Sniffs\\Classes\\ClassDeclarationSniff',
            'PHP_CodeSniffer\\Standards\\PSR1\\Sniffs\\Files\\SideEffectsSniff',
            'PHP_CodeSniffer\\Standards\\PSR1\\Sniffs\\Methods\\CamelCapsMethodNameSniff',
            'PHP_CodeSniffer\\Standards\\Squiz\\Sniffs\\Classes\\ValidClassNameSniff',
        ];

        return [
            'No restrictions, cache off'     => [
                'cliArgs'  => ['--standard=PSR1'],
                'cache'    => false,
                'expected' => $completeSet,
            ],
            'Has exclusions, cache off'      => [
                'cliArgs'  => [
                    '--standard=PSR1',
                    '--exclude=Generic.Files.ByteOrderMark,Generic.PHP.DisallowShortOpenTag,PSR1.Files.SideEffects,Generic.PHP.DisallowAlternativePHPTags',
                ],
                'cache'    => false,
                'expected' => [
                    'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\NamingConventions\\UpperCaseConstantNameSniff',
                    'PHP_CodeSniffer\\Standards\\PSR1\\Sniffs\\Classes\\ClassDeclarationSniff',
                    'PHP_CodeSniffer\\Standards\\PSR1\\Sniffs\\Methods\\CamelCapsMethodNameSniff',
                    'PHP_CodeSniffer\\Standards\\Squiz\\Sniffs\\Classes\\ValidClassNameSniff',
                ],
            ],
            'Has sniff selection, cache off' => [
                'cliArgs'  => [
                    '--standard=PSR1',
                    '--sniffs=Generic.Files.ByteOrderMark,Generic.PHP.DisallowShortOpenTag,PSR1.Files.SideEffects,Generic.PHP.DisallowAlternativePHPTags',
                ],
                'cache'    => false,
                'expected' => [
                    'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\Files\\ByteOrderMarkSniff',
                    'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\PHP\\DisallowAlternativePHPTagsSniff',
                    'PHP_CodeSniffer\\Standards\\Generic\\Sniffs\\PHP\\DisallowShortOpenTagSniff',
                    'PHP_CodeSniffer\\Standards\\PSR1\\Sniffs\\Files\\SideEffectsSniff',
                ],
            ],
            'No restrictions, cache on'      => [
                'cliArgs'  => ['--standard=PSR1'],
                'cache'    => true,
                'expected' => $completeSet,
            ],
            'Has exclusions, cache on'       => [
                'cliArgs'  => [
                    '--standard=PSR1',
                    '--exclude=Generic.Files.ByteOrderMark,Generic.PHP.DisallowAlternativePHPTags,Generic.PHP.DisallowShortOpenTag,PSR1.Files.SideEffects',
                ],
                'cache'    => true,
                'expected' => $completeSet,
            ],

            /*
             * "Has sniff selection, cache on" case cannot be tested due to the `Ruleset` class
             * containing special handling of sniff selection when the tests are running.
             */

        ];

    }//end dataCachingVersusRestrictions()


    /**
     * Test an exception is thrown when no sniffs have been registered via the ruleset.
     *
     * @return void
     */
    public function testNoSniffsRegisteredException()
    {
        $standard = __DIR__.'/ConstructorNoSniffsTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);

        $message = 'ERROR: No sniffs were registered.'.PHP_EOL.PHP_EOL;
        $this->expectRuntimeExceptionMessage($message);

        new Ruleset($config);

    }//end testNoSniffsRegisteredException()


}//end class
