<?php
/**
 * Tests progress reporting in the Runner class.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Runner;

use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Runner;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Tests progress reporting.
 *
 * @covers \PHP_CodeSniffer\Runner::printProgress
 */
final class PrintProgressTest extends TestCase
{

    /**
     * Config instance for use in the tests.
     *
     * @var \PHP_CodeSniffer\Config
     */
    private static $config;

    /**
     * Ruleset instance for use in the tests.
     *
     * @var \PHP_CodeSniffer\Ruleset
     */
    private static $ruleset;

    /**
     * Runner instance for use in the tests.
     *
     * @var \PHP_CodeSniffer\Runner
     */
    private static $runner;

    /**
     * File instance for use in the tests.
     *
     * @var \PHP_CodeSniffer\Files\File
     */
    private static $fileWithoutErrorsOrWarnings;


    /**
     * Create some re-usable objects for use in the tests.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function initializeConfigRulesetRunner()
    {
        self::$config            = new ConfigDouble(['-p']);
        self::$config->standards = ['Generic'];
        self::$config->sniffs    = ['Generic.PHP.LowerCaseConstant'];
        self::$ruleset           = new Ruleset(self::$config);

        self::$runner         = new Runner();
        self::$runner->config = self::$config;

        // Simple file which won't have any errors against the above sniff.
        $content = '<?php'."\n".'$var = false;'."\n";
        self::$fileWithoutErrorsOrWarnings = new DummyFile($content, self::$ruleset, self::$config);
        self::$fileWithoutErrorsOrWarnings->process();

    }//end initializeConfigRulesetRunner()


    /**
     * Reset some flags between tests.
     *
     * @after
     *
     * @return void
     */
    protected function resetObjectFlags()
    {
        self::$config->showProgress = true;
        self::$fileWithoutErrorsOrWarnings->ignored = false;

    }//end resetObjectFlags()


    /**
     * Destroy the Config object after the test to reset statics.
     *
     * @afterClass
     *
     * @return void
     */
    public static function reset()
    {
        // Explicitly trigger __destruct() on the ConfigDouble to reset the Config statics.
        // The explicit method call prevents potential stray test-local references to the $config object
        // preventing the destructor from running the clean up (which without stray references would be
        // automagically triggered when this object is destroyed, but we can't definitively rely on that).
        self::$config->__destruct();

    }//end reset()


    /**
     * Verify that if progress reporting is disabled, no progress dots will show.
     *
     * @return void
     */
    public function testNoProgressIsShownWhenDisabled()
    {
        $this->expectOutputString('');

        self::$config->showProgress = false;

        for ($i = 1; $i <= 10; $i++) {
            self::$runner->printProgress(self::$fileWithoutErrorsOrWarnings, 3, $i);
        }

    }//end testNoProgressIsShownWhenDisabled()


    /**
     * Verify ignored files will be marked with an "S" for "skipped".
     *
     * @return void
     */
    public function testProgressDotSkippedFiles()
    {
        $nrOfFiles = 10;
        $this->expectOutputString('.S.S.S.S.S 10 / 10 (100%)'.PHP_EOL);

        for ($i = 1; $i <= $nrOfFiles; $i++) {
            if (($i % 2) === 0) {
                self::$fileWithoutErrorsOrWarnings->ignored = true;
            } else {
                self::$fileWithoutErrorsOrWarnings->ignored = false;
            }

            self::$runner->printProgress(self::$fileWithoutErrorsOrWarnings, $nrOfFiles, $i);
        }

    }//end testProgressDotSkippedFiles()


    /**
     * Verify the handling of the summary at the end of each line.
     *
     * @param int    $nrOfFiles The number of files in the scan.
     * @param string $expected  The expected progress information output.
     *
     * @dataProvider dataEndOfLineSummary
     *
     * @return void
     */
    public function testEndOfLineSummary($nrOfFiles, $expected)
    {
        $this->expectOutputString($expected);

        for ($i = 1; $i <= $nrOfFiles; $i++) {
            self::$runner->printProgress(self::$fileWithoutErrorsOrWarnings, $nrOfFiles, $i);
        }

    }//end testEndOfLineSummary()


    /**
     * Data provider.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataEndOfLineSummary()
    {
        $fullLineOfDots = str_repeat('.', 60);

        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- Favour test readability.
        return [
            'Less than 60 files (23)'       => [
                'nrOfFiles' => 23,
                'expected'  => str_repeat('.', 23).' 23 / 23 (100%)'.PHP_EOL,
            ],
            'Exactly 60 files'              => [
                'nrOfFiles' => 60,
                'expected'  => $fullLineOfDots.' 60 / 60 (100%)'.PHP_EOL,
            ],
            'Between 60 and 120 files (71)' => [
                'nrOfFiles' => 71,
                'expected'  => $fullLineOfDots.' 60 / 71 (85%)'.PHP_EOL
                    .str_repeat('.', 11).str_repeat(' ', 49).' 71 / 71 (100%)'.PHP_EOL,
            ],
            'More than 120 files (162)'     => [
                'nrOfFiles' => 162,
                'expected'  => $fullLineOfDots.'  60 / 162 (37%)'.PHP_EOL
                    .$fullLineOfDots.' 120 / 162 (74%)'.PHP_EOL
                    .str_repeat('.', 42).str_repeat(' ', 18).' 162 / 162 (100%)'.PHP_EOL,
            ],
            // More than anything, this tests that the padding of the numbers is handled correctly.
            'More than 1000 files (1234)'   => [
                'nrOfFiles' => 1234,
                'expected'  => $fullLineOfDots.'   60 / 1234 (5%)'.PHP_EOL
                    .$fullLineOfDots.'  120 / 1234 (10%)'.PHP_EOL
                    .$fullLineOfDots.'  180 / 1234 (15%)'.PHP_EOL
                    .$fullLineOfDots.'  240 / 1234 (19%)'.PHP_EOL
                    .$fullLineOfDots.'  300 / 1234 (24%)'.PHP_EOL
                    .$fullLineOfDots.'  360 / 1234 (29%)'.PHP_EOL
                    .$fullLineOfDots.'  420 / 1234 (34%)'.PHP_EOL
                    .$fullLineOfDots.'  480 / 1234 (39%)'.PHP_EOL
                    .$fullLineOfDots.'  540 / 1234 (44%)'.PHP_EOL
                    .$fullLineOfDots.'  600 / 1234 (49%)'.PHP_EOL
                    .$fullLineOfDots.'  660 / 1234 (53%)'.PHP_EOL
                    .$fullLineOfDots.'  720 / 1234 (58%)'.PHP_EOL
                    .$fullLineOfDots.'  780 / 1234 (63%)'.PHP_EOL
                    .$fullLineOfDots.'  840 / 1234 (68%)'.PHP_EOL
                    .$fullLineOfDots.'  900 / 1234 (73%)'.PHP_EOL
                    .$fullLineOfDots.'  960 / 1234 (78%)'.PHP_EOL
                    .$fullLineOfDots.' 1020 / 1234 (83%)'.PHP_EOL
                    .$fullLineOfDots.' 1080 / 1234 (88%)'.PHP_EOL
                    .$fullLineOfDots.' 1140 / 1234 (92%)'.PHP_EOL
                    .$fullLineOfDots.' 1200 / 1234 (97%)'.PHP_EOL
                    .str_repeat('.', 34).str_repeat(' ', 26).' 1234 / 1234 (100%)'.PHP_EOL,
            ],
        ];
        // phpcs:enable

    }//end dataEndOfLineSummary()


}//end class
