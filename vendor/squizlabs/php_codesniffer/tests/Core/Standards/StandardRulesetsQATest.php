<?php
/**
 * Tests that pre-defined standards do not throw errors.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Standards;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Core\Ruleset\AbstractRulesetTestCase;

/**
 * Tests that pre-defined standards do not throw errors.
 *
 * @coversNothing
 */
final class StandardRulesetsQATest extends AbstractRulesetTestCase
{


    /**
     * QA check: verify that the PHPCS native rulesets do not throw any errors or other messages.
     *
     * This QA check will prevent issues like:
     * - a sniff being removed, but still being referenced from within a PHPCS native ruleset.
     * - a supported feature being removed, but still being used from within a PHPCS native ruleset.
     *
     * @param string $standard The name of the build-in standard to test.
     *
     * @dataProvider dataBuildInStandards
     *
     * @return void
     */
    public function testBuildInStandardsDoNotContainErrors($standard)
    {
        ob_start();
        $config  = new ConfigDouble(["--standard=$standard"]);
        $ruleset = new Ruleset($config);

        $seenOutput = ob_get_contents();
        ob_end_clean();

        // Make sure no messages were thrown.
        $this->assertSame('', $seenOutput);

        // Make sure sniffs were registered.
        $this->assertGreaterThanOrEqual(1, count($ruleset->sniffCodes));

    }//end testBuildInStandardsDoNotContainErrors()


    /**
     * Data provider.
     *
     * @see self::testBuildInStandardsDoNotContainErrors()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataBuildInStandards()
    {
        // Get a list of all build-in, PHPCS native standards.
        $sep          = DIRECTORY_SEPARATOR;
        $targetDir    = dirname(dirname(dirname(__DIR__))).$sep.'src'.$sep.'Standards'.$sep;
        $rulesetFiles = glob($targetDir.'*'.$sep.'ruleset.xml');

        $data = [];
        foreach ($rulesetFiles as $file) {
            $standardName        = basename(dirname($file));
            $data[$standardName] = [
                'standard' => $standardName,
            ];
        }

        return $data;

    }//end dataBuildInStandards()


}//end class
