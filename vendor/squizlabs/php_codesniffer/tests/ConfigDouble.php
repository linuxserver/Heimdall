<?php
/**
 * Config class for use in the tests.
 *
 * The Config class contains a number of static properties.
 * As the value of these static properties will be retained between instantiations of the class,
 * config values set in one test can influence the results for another test, which makes tests unstable.
 *
 * This class is a "double" of the Config class which prevents this from happening.
 * In _most_ cases, tests should be using this class instead of the "normal" Config,
 * with the exception of select tests for the Config class itself.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests;

use PHP_CodeSniffer\Config;
use ReflectionProperty;

final class ConfigDouble extends Config
{

    /**
     * Whether or not the setting of a standard should be skipped.
     *
     * @var boolean
     */
    private $skipSettingStandard = false;


    /**
     * Creates a clean Config object and populates it with command line values.
     *
     * @param array<string> $cliArgs                An array of values gathered from CLI args.
     * @param bool          $skipSettingStandard    Whether to skip setting a standard to prevent
     *                                              the Config class trying to auto-discover a ruleset file.
     *                                              Should only be set to `true` for tests which actually test
     *                                              the ruleset auto-discovery.
     *                                              Note: there is no need to set this to `true` when a standard
     *                                              is being passed via the `$cliArgs`. Those settings will always
     *                                              be respected.
     *                                              Defaults to `false`. Will result in the standard being set
     *                                              to "PSR1" if not provided via `$cliArgs`.
     * @param bool          $skipSettingReportWidth Whether to skip setting a report-width to prevent
     *                                              the Config class trying to auto-discover the screen width.
     *                                              Should only be set to `true` for tests which actually test
     *                                              the screen width auto-discovery.
     *                                              Note: there is no need to set this to `true` when a report-width
     *                                              is being passed via the `$cliArgs`. Those settings will always
     *                                              respected.
     *                                              Defaults to `false`. Will result in the reportWidth being set
     *                                              to "80" if not provided via `$cliArgs`.
     *
     * @return void
     */
    public function __construct(array $cliArgs=[], $skipSettingStandard=false, $skipSettingReportWidth=false)
    {
        $this->skipSettingStandard = $skipSettingStandard;

        $this->resetSelectProperties();
        $this->preventReadingCodeSnifferConfFile();

        parent::__construct($cliArgs);

        if ($skipSettingReportWidth !== true) {
            $this->preventAutoDiscoveryScreenWidth();
        }

    }//end __construct()


    /**
     * Ensures the static properties in the Config class are reset to their default values
     * when the ConfigDouble is no longer used.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->setStaticConfigProperty('overriddenDefaults', []);
        $this->setStaticConfigProperty('executablePaths', []);
        $this->setStaticConfigProperty('configData', null);
        $this->setStaticConfigProperty('configDataFile', null);

    }//end __destruct()


    /**
     * Sets the command line values and optionally prevents a file system search for a custom ruleset.
     *
     * @param array<string> $args An array of command line arguments to set.
     *
     * @return void
     */
    public function setCommandLineValues($args)
    {
        parent::setCommandLineValues($args);

        if ($this->skipSettingStandard !== true) {
            $this->preventSearchingForRuleset();
        }

    }//end setCommandLineValues()


    /**
     * Reset a few properties on the Config class to their default values.
     *
     * @return void
     */
    private function resetSelectProperties()
    {
        $this->setStaticConfigProperty('overriddenDefaults', []);
        $this->setStaticConfigProperty('executablePaths', []);

    }//end resetSelectProperties()


    /**
     * Prevent the values in a potentially available user-specific `CodeSniffer.conf` file
     * from influencing the tests.
     *
     * This also prevents some file system calls which can influence the test runtime.
     *
     * @return void
     */
    private function preventReadingCodeSnifferConfFile()
    {
        $this->setStaticConfigProperty('configData', []);
        $this->setStaticConfigProperty('configDataFile', '');

    }//end preventReadingCodeSnifferConfFile()


    /**
     * Prevent searching for a custom ruleset by setting a standard, but only if the test
     * being run doesn't set a standard itself.
     *
     * This also prevents some file system calls which can influence the test runtime.
     *
     * The standard being set is the smallest one available so the ruleset initialization
     * will be the fastest possible.
     *
     * @return void
     */
    private function preventSearchingForRuleset()
    {
        $overriddenDefaults = $this->getStaticConfigProperty('overriddenDefaults');
        if (isset($overriddenDefaults['standards']) === false) {
            $this->standards = ['PSR1'];
            $overriddenDefaults['standards'] = true;
        }

        self::setStaticConfigProperty('overriddenDefaults', $overriddenDefaults);

    }//end preventSearchingForRuleset()


    /**
     * Prevent a call to stty to figure out the screen width, but only if the test being run
     * doesn't set a report width itself.
     *
     * @return void
     */
    private function preventAutoDiscoveryScreenWidth()
    {
        $settings = $this->getSettings();
        if ($settings['reportWidth'] === 'auto') {
            $this->reportWidth = self::DEFAULT_REPORT_WIDTH;
        }

    }//end preventAutoDiscoveryScreenWidth()


    /**
     * Helper function to retrieve the value of a private static property on the Config class.
     *
     * @param string $name The name of the property to retrieve.
     *
     * @return mixed
     */
    private function getStaticConfigProperty($name)
    {
        $property = new ReflectionProperty('PHP_CodeSniffer\Config', $name);
        $property->setAccessible(true);
        return $property->getValue();

    }//end getStaticConfigProperty()


    /**
     * Helper function to set the value of a private static property on the Config class.
     *
     * @param string $name  The name of the property to set.
     * @param mixed  $value The value to set the property to.
     *
     * @return void
     */
    private function setStaticConfigProperty($name, $value)
    {
        $property = new ReflectionProperty('PHP_CodeSniffer\Config', $name);
        $property->setAccessible(true);
        $property->setValue(null, $value);
        $property->setAccessible(false);

    }//end setStaticConfigProperty()


}//end class
