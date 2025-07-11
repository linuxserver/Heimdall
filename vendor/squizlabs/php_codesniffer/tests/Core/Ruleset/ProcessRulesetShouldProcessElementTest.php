<?php
/**
 * Test handling of `phpc(cs|cbf)-only` instructions at ruleset level.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Test handling of `phpc(cs|cbf)-only` instructions at ruleset level.
 *
 * @covers \PHP_CodeSniffer\Ruleset::processRuleset
 * @covers \PHP_CodeSniffer\Ruleset::shouldProcessElement
 */
final class ProcessRulesetShouldProcessElementTest extends TestCase
{

    /**
     * Cache to store the original ini values for ini settings being changed in these tests.
     *
     * @var array<string, string|null>
     */
    private static $originalIniValues = [
        'bcmath.scale' => null,
        'docref_root'  => null,
        'user_agent'   => null,
    ];

    /**
     * The Config object.
     *
     * @var \PHP_CodeSniffer\Tests\ConfigDouble
     */
    private static $config;

    /**
     * The Ruleset object.
     *
     * @var \PHP_CodeSniffer\Ruleset
     */
    private static $ruleset;


    /**
     * Store the original ini values to allow for restoring them after the tests.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function saveOriginalIniValues()
    {
        foreach (self::$originalIniValues as $name => $null) {
            $value = ini_get($name);
            if ($value !== false) {
                self::$originalIniValues[$name] = $value;
            }
        }

    }//end saveOriginalIniValues()


    /**
     * Initialize the config and ruleset objects for this test only once (but do allow recording code coverage).
     *
     * @before
     *
     * @return void
     */
    protected function initializeConfigAndRuleset()
    {
        if (isset(self::$ruleset) === false) {
            // Set up the ruleset.
            $standard      = __DIR__.'/ProcessRulesetShouldProcessElementTest.xml';
            self::$config  = new ConfigDouble(["--standard=$standard"]);
            self::$ruleset = new Ruleset(self::$config);
        }

    }//end initializeConfigAndRuleset()


    /**
     * Destroy the Config object and restore the ini values after the tests.
     *
     * @afterClass
     *
     * @return void
     */
    public static function restoreOriginalValues()
    {
        // Explicitly trigger __destruct() on the ConfigDouble to reset the Config statics.
        // The explicit method call prevents potential stray test-local references to the $config object
        // preventing the destructor from running the clean up (which without stray references would be
        // automagically triggered when this object is destroyed, but we can't definitively rely on that).
        if (isset(self::$config) === true) {
            self::$config->__destruct();
        }

        foreach (self::$originalIniValues as $name => $value) {
            if ($value === null) {
                continue;
            }

            ini_set($name, $value);
        }

    }//end restoreOriginalValues()


    /**
     * Verify that in CS mode, phpcs-only <config> directives are respected and phpcbf-only <config>
     * directives are ignored.
     *
     * @return void
     */
    public function testShouldProcessConfigCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $this->assertSame('true', Config::getConfigData('neither'), 'Non-selective config directive was not applied.');
        $this->assertSame('true', Config::getConfigData('csOnly'), 'CS-only config directive was not applied.');
        $this->assertSame(null, Config::getConfigData('cbfOnly'), 'CBF-only config directive was applied, while it shouldn\'t have been.');

    }//end testShouldProcessConfigCsonly()


    /**
     * Verify that in CBF mode, phpcbf-only <config> directives are respected and phpcs-only <config>
     * directives are ignored.
     *
     * @group CBF
     *
     * @return void
     */
    public function testShouldProcessConfigCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $this->assertSame('true', Config::getConfigData('neither'), 'Non-selective config directive was not applied.');
        $this->assertSame(null, Config::getConfigData('csOnly'), 'CS-only config directive was applied, while it shouldn\'t have been.');
        $this->assertSame('true', Config::getConfigData('cbfOnly'), 'CBF-only config directive was not applied.');

    }//end testShouldProcessConfigCbfonly()


    /**
     * Verify that in CS mode, phpcs-only <arg> directives are respected and phpcbf-only <arg>
     * directives are ignored.
     *
     * @return void
     */
    public function testShouldProcessArgCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $expectedExtensions = [
            'php'  => 'PHP',
            'phpt' => 'PHP',
        ];
        $expectedReports    = ['full' => null];

        $this->assertSame($expectedExtensions, self::$config->extensions, 'Non-selective arg directive was not applied.');
        $this->assertTrue(self::$config->showProgress, 'Non-selective short arg directive was not applied [1].');
        $this->assertTrue(self::$config->showSources, 'Non-selective short arg directive was not applied [2].');
        $this->assertTrue(self::$config->colors, 'CS-only arg directive was not applied.');
        $this->assertSame($expectedReports, self::$config->reports, 'CBF-only arg directive was applied, while it shouldn\'t have been.');

    }//end testShouldProcessArgCsonly()


    /**
     * Verify that in CBF mode, phpcbf-only <arg> directives are respected and phpcs-only <arg>
     * directives are ignored.
     *
     * @group CBF
     *
     * @return void
     */
    public function testShouldProcessArgCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $expectedExtensions = [
            'php'  => 'PHP',
            'phpt' => 'PHP',
        ];
        $expectedReports    = ['summary' => null];

        $this->assertSame($expectedExtensions, self::$config->extensions, 'Non-selective arg directive was not applied.');
        $this->assertTrue(self::$config->showProgress, 'Non-selective short arg directive was not applied [1].');
        $this->assertTrue(self::$config->showSources, 'Non-selective short arg directive was not applied [2].');
        $this->assertFalse(self::$config->colors, 'CS-only arg directive was applied, while it shouldn\'t have been.');
        $this->assertSame($expectedReports, self::$config->reports, 'CBF-only arg directive was not applied.');

    }//end testShouldProcessArgCbfonly()


    /**
     * Verify that in CS mode, phpcs-only <ini> directives are respected and phpcbf-only <ini>
     * directives are ignored.
     *
     * @requires extension bcmath
     *
     * @return void
     */
    public function testShouldProcessIniCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $this->assertSame('2', ini_get('bcmath.scale'), 'Non-selective ini directive was not applied.');
        $this->assertSame('path/to/docs/', ini_get('docref_root'), 'CS-only ini directive was not applied.');
        $this->assertSame('', ini_get('user_agent'), 'CBF-only ini directive was applied, while it shouldn\'t have been.');

    }//end testShouldProcessIniCsonly()


    /**
     * Verify that in CBF mode, phpcbf-only <ini> directives are respected and phpcs-only <ini>
     * directives are ignored.
     *
     * @group    CBF
     * @requires extension bcmath
     *
     * @return void
     */
    public function testShouldProcessIniCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $this->assertSame('2', ini_get('bcmath.scale'), 'Non-selective ini directive was not applied.');
        $this->assertSame('', ini_get('docref_root'), 'CS-only ini directive was applied, while it shouldn\'t have been..');
        $this->assertSame('Never mind', ini_get('user_agent'), 'CBF-only ini directive was not applied.');

    }//end testShouldProcessIniCbfonly()


    /**
     * Verify that in CS mode, phpcs-only <exclude-pattern> directives are respected and phpcbf-only <exclude-pattern>
     * directives are ignored.
     *
     * @return void
     */
    public function testShouldProcessExcludePatternCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $expected = [
            './tests/'  => 'absolute',
            './vendor/' => 'absolute',
        ];

        $this->assertSame($expected, self::$ruleset->ignorePatterns);

    }//end testShouldProcessExcludePatternCsonly()


    /**
     * Verify that in CBF mode, phpcbf-only <exclude-pattern> directives are respected and phpcs-only <exclude-pattern>
     * directives are ignored.
     *
     * @group CBF
     *
     * @return void
     */
    public function testShouldProcessExcludePatternCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $expected = [
            './tests/'        => 'absolute',
            './node-modules/' => 'absolute',
        ];

        $this->assertSame($expected, self::$ruleset->ignorePatterns);

    }//end testShouldProcessExcludePatternCbfonly()


    /**
     * Verify that in CS mode, phpcs-only <rule> directives are respected and phpcbf-only <rule>
     * directives are ignored.
     *
     * @return void
     */
    public function testShouldProcessRuleCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $this->assertArrayHasKey('PEAR.Formatting.MultiLineAssignment', self::$ruleset->sniffCodes);
        $this->assertArrayHasKey('Generic.Arrays.ArrayIndent', self::$ruleset->sniffCodes);
        $this->assertArrayNotHasKey('PSR2.Classes.ClassDeclaration', self::$ruleset->sniffCodes);

    }//end testShouldProcessRuleCsonly()


    /**
     * Verify that in CBF mode, phpcbf-only <rule> directives are respected and phpcs-only <rule>
     * directives are ignored.
     *
     * @group CBF
     *
     * @return void
     */
    public function testShouldProcessRuleCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $this->assertArrayHasKey('PEAR.Formatting.MultiLineAssignment', self::$ruleset->sniffCodes);
        $this->assertArrayNotHasKey('Generic.Arrays.ArrayIndent', self::$ruleset->sniffCodes);
        $this->assertArrayHasKey('PSR2.Classes.ClassDeclaration', self::$ruleset->sniffCodes);

    }//end testShouldProcessRuleCbfonly()


    /**
     * Verify that in CS mode, phpcs-only <exclude> in <rule> directives are respected and phpcbf-only <exclude> in <rule>
     * directives are ignored.
     *
     * @return void
     */
    public function testShouldProcessRuleExcludeCsonly()
    {
        if (PHP_CODESNIFFER_CBF === true) {
            $this->markTestSkipped('This test needs CS mode to run');
        }

        $expected = [
            'PEAR.Formatting.MultiLineAssignment.Indent' => [
                'severity' => 0,
            ],
        ];

        $this->assertSame($expected, self::$ruleset->ruleset);

    }//end testShouldProcessRuleExcludeCsonly()


    /**
     * Verify that in CBF mode, phpcbf-only <exclude> in <rule> directives are respected and phpcs-only <exclude> in <rule>
     * directives are ignored.
     *
     * @group CBF
     *
     * @return void
     */
    public function testShouldProcessRuleExcludeCbfonly()
    {
        if (PHP_CODESNIFFER_CBF === false) {
            $this->markTestSkipped('This test needs CBF mode to run');
        }

        $expected = [
            'PEAR.Formatting.MultiLineAssignment.EqualSignLine' => [
                'severity' => 0,
            ],
        ];

        $this->assertSame($expected, self::$ruleset->ruleset);

    }//end testShouldProcessRuleExcludeCbfonly()


}//end class
