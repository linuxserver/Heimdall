<?php
/**
 * Tests PHPCS native handling of sniff deprecations.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Tests PHPCS native handling of sniff deprecations.
 *
 * @covers \PHP_CodeSniffer\Ruleset::hasSniffDeprecations
 * @covers \PHP_CodeSniffer\Ruleset::showSniffDeprecations
 */
final class ShowSniffDeprecationsTest extends TestCase
{


    /**
     * Test the return value of the hasSniffDeprecations() method.
     *
     * @param string $standard The standard to use for the test.
     * @param bool   $expected The expected function return value.
     *
     * @dataProvider dataHasSniffDeprecations
     *
     * @return void
     */
    public function testHasSniffDeprecations($standard, $expected)
    {
        $config  = new ConfigDouble(['.', "--standard=$standard"]);
        $ruleset = new Ruleset($config);

        $this->assertSame($expected, $ruleset->hasSniffDeprecations());

    }//end testHasSniffDeprecations()


    /**
     * Data provider.
     *
     * @see testHasSniffDeprecations()
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataHasSniffDeprecations()
    {
        return [
            'Standard not using deprecated sniffs: PSR1'     => [
                'standard' => 'PSR1',
                'expected' => false,
            ],
            'Standard using deprecated sniffs: Test Fixture' => [
                'standard' => __DIR__.'/ShowSniffDeprecationsTest.xml',
                'expected' => true,
            ],
        ];

    }//end dataHasSniffDeprecations()


    /**
     * Test that the listing with deprecated sniffs will not show when specific command-line options are being used.
     *
     * @param string        $standard       The standard to use for the test.
     * @param array<string> $additionalArgs Optional. Additional arguments to pass.
     *
     * @dataProvider dataDeprecatedSniffsListDoesNotShow
     *
     * @return void
     */
    public function testDeprecatedSniffsListDoesNotShow($standard, $additionalArgs=[])
    {
        $args   = $additionalArgs;
        $args[] = '.';
        $args[] = "--standard=$standard";

        $config  = new ConfigDouble($args);
        $ruleset = new Ruleset($config);

        $this->expectOutputString('');

        $ruleset->showSniffDeprecations();

    }//end testDeprecatedSniffsListDoesNotShow()


    /**
     * Data provider.
     *
     * @see testDeprecatedSniffsListDoesNotShow()
     *
     * @return array<string, array<string, string|array<string>>>
     */
    public static function dataDeprecatedSniffsListDoesNotShow()
    {
        return [
            'Standard not using deprecated sniffs: PSR1'                   => [
                'standard' => 'PSR1',
            ],
            'Standard using deprecated sniffs; explain mode'               => [
                'standard'       => __DIR__.'/ShowSniffDeprecationsTest.xml',
                'additionalArgs' => ['-e'],
            ],
            'Standard using deprecated sniffs; quiet mode'                 => [
                'standard'       => __DIR__.'/ShowSniffDeprecationsTest.xml',
                'additionalArgs' => ['-q'],
            ],
            'Standard using deprecated sniffs; documentation is requested' => [
                'standard'       => __DIR__.'/ShowSniffDeprecationsTest.xml',
                'additionalArgs' => ['--generator=text'],
            ],
        ];

    }//end dataDeprecatedSniffsListDoesNotShow()


    /**
     * Test that the listing with deprecated sniffs will not show when using a standard containing deprecated sniffs,
     * but only running select non-deprecated sniffs (using `--sniffs=...`).
     *
     * @return void
     */
    public function testDeprecatedSniffsListDoesNotShowWhenSelectedSniffsAreNotDeprecated()
    {
        $standard = __DIR__.'/ShowSniffDeprecationsTest.xml';
        $config   = new ConfigDouble(['.', "--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        /*
         * Apply sniff restrictions.
         * For tests we need to manually trigger this if the standard is "installed", like with the fixtures these tests use.
         */

        $restrictions = [];
        $sniffs       = [
            'Fixtures.SetProperty.AllowedAsDeclared',
            'Fixtures.SetProperty.AllowedViaStdClass',
        ];
        foreach ($sniffs as $sniffCode) {
            $parts     = explode('.', strtolower($sniffCode));
            $sniffName = $parts[0].'\sniffs\\'.$parts[1].'\\'.$parts[2].'sniff';
            $restrictions[strtolower($sniffName)] = true;
        }

        $sniffFiles = [];
        $allSniffs  = $ruleset->sniffCodes;
        foreach ($allSniffs as $sniffName) {
            $sniffFile    = str_replace('\\', DIRECTORY_SEPARATOR, $sniffName);
            $sniffFile    = __DIR__.DIRECTORY_SEPARATOR.$sniffFile.'.php';
            $sniffFiles[] = $sniffFile;
        }

        $ruleset->registerSniffs($allSniffs, $restrictions, []);
        $ruleset->populateTokenListeners();

        $this->expectOutputString('');

        $ruleset->showSniffDeprecations();

    }//end testDeprecatedSniffsListDoesNotShowWhenSelectedSniffsAreNotDeprecated()


    /**
     * Test that the listing with deprecated sniffs will not show when using a standard containing deprecated sniffs,
     * but all deprecated sniffs have been excluded from the run (using `--exclude=...`).
     *
     * @return void
     */
    public function testDeprecatedSniffsListDoesNotShowWhenAllDeprecatedSniffsAreExcluded()
    {
        $standard = __DIR__.'/ShowSniffDeprecationsTest.xml';
        $config   = new ConfigDouble(['.', "--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        /*
         * Apply sniff restrictions.
         * For tests we need to manually trigger this if the standard is "installed", like with the fixtures these tests use.
         */

        $exclusions = [];
        $exclude    = [
            'Fixtures.Deprecated.WithLongReplacement',
            'Fixtures.Deprecated.WithoutReplacement',
            'Fixtures.Deprecated.WithReplacement',
            'Fixtures.Deprecated.WithReplacementContainingLinuxNewlines',
            'Fixtures.Deprecated.WithReplacementContainingNewlines',
        ];
        foreach ($exclude as $sniffCode) {
            $parts     = explode('.', strtolower($sniffCode));
            $sniffName = $parts[0].'\sniffs\\'.$parts[1].'\\'.$parts[2].'sniff';
            $exclusions[strtolower($sniffName)] = true;
        }

        $sniffFiles = [];
        $allSniffs  = $ruleset->sniffCodes;
        foreach ($allSniffs as $sniffName) {
            $sniffFile    = str_replace('\\', DIRECTORY_SEPARATOR, $sniffName);
            $sniffFile    = __DIR__.DIRECTORY_SEPARATOR.$sniffFile.'.php';
            $sniffFiles[] = $sniffFile;
        }

        $ruleset->registerSniffs($allSniffs, [], $exclusions);
        $ruleset->populateTokenListeners();

        $this->expectOutputString('');

        $ruleset->showSniffDeprecations();

    }//end testDeprecatedSniffsListDoesNotShowWhenAllDeprecatedSniffsAreExcluded()


    /**
     * Test deprecated sniffs are listed alphabetically in the deprecated sniffs warning.
     *
     * This tests a number of different aspects:
     * 1. That the summary line uses the correct grammar when there is are multiple deprecated sniffs.
     * 2. That there is no trailing whitespace when the sniff does not provide a custom message.
     * 3. That custom messages containing new line characters (any type) are handled correctly and
     *    that those new line characters are converted to the OS supported new line char.
     *
     * @return void
     */
    public function testDeprecatedSniffsWarning()
    {
        $standard = __DIR__.'/ShowSniffDeprecationsTest.xml';
        $config   = new ConfigDouble(["--standard=$standard", '--no-colors']);
        $ruleset  = new Ruleset($config);

        $expected  = 'WARNING: The SniffDeprecationTest standard uses 5 deprecated sniffs'.PHP_EOL;
        $expected .= '--------------------------------------------------------------------------------'.PHP_EOL;
        $expected .= '-  Fixtures.Deprecated.WithLongReplacement'.PHP_EOL;
        $expected .= '   This sniff has been deprecated since v3.8.0 and will be removed in v4.0.0.'.PHP_EOL;
        $expected .= '   Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vel'.PHP_EOL;
        $expected .= '   vestibulum nunc. Sed luctus dolor tortor, eu euismod purus pretium sed.'.PHP_EOL;
        $expected .= '   Fusce egestas congue massa semper cursus. Donec quis pretium tellus. In'.PHP_EOL;
        $expected .= '   lacinia, augue ut ornare porttitor, diam nunc faucibus purus, et accumsan'.PHP_EOL;
        $expected .= '   eros sapien at sem. Sed pulvinar aliquam malesuada. Aliquam erat volutpat.'.PHP_EOL;
        $expected .= '   Mauris gravida rutrum lectus at egestas. Fusce tempus elit in tincidunt'.PHP_EOL;
        $expected .= '   dictum. Suspendisse dictum egestas sapien, eget ullamcorper metus elementum'.PHP_EOL;
        $expected .= '   semper. Vestibulum sem justo, consectetur ac tincidunt et, finibus eget'.PHP_EOL;
        $expected .= '   libero.'.PHP_EOL;
        $expected .= '-  Fixtures.Deprecated.WithoutReplacement'.PHP_EOL;
        $expected .= '   This sniff has been deprecated since v3.4.0 and will be removed in v4.0.0.'.PHP_EOL;
        $expected .= '-  Fixtures.Deprecated.WithReplacement'.PHP_EOL;
        $expected .= '   This sniff has been deprecated since v3.8.0 and will be removed in v4.0.0.'.PHP_EOL;
        $expected .= '   Use the Stnd.Category.OtherSniff sniff instead.'.PHP_EOL;
        $expected .= '-  Fixtures.Deprecated.WithReplacementContainingLinuxNewlines'.PHP_EOL;
        $expected .= '   This sniff has been deprecated since v3.8.0 and will be removed in v4.0.0.'.PHP_EOL;
        $expected .= '   Lorem ipsum dolor sit amet, consectetur adipiscing elit.'.PHP_EOL;
        $expected .= '   Fusce vel vestibulum nunc. Sed luctus dolor tortor, eu euismod purus pretium'.PHP_EOL;
        $expected .= '   sed.'.PHP_EOL;
        $expected .= '   Fusce egestas congue massa semper cursus. Donec quis pretium tellus.'.PHP_EOL;
        $expected .= '   In lacinia, augue ut ornare porttitor, diam nunc faucibus purus, et accumsan'.PHP_EOL;
        $expected .= '   eros sapien at sem.'.PHP_EOL;
        $expected .= '   Sed pulvinar aliquam malesuada. Aliquam erat volutpat. Mauris gravida rutrum'.PHP_EOL;
        $expected .= '   lectus at egestas.'.PHP_EOL;
        $expected .= '-  Fixtures.Deprecated.WithReplacementContainingNewlines'.PHP_EOL;
        $expected .= '   This sniff has been deprecated since v3.8.0 and will be removed in v4.0.0.'.PHP_EOL;
        $expected .= '   Lorem ipsum dolor sit amet, consectetur adipiscing elit.'.PHP_EOL;
        $expected .= '   Fusce vel vestibulum nunc. Sed luctus dolor tortor, eu euismod purus pretium'.PHP_EOL;
        $expected .= '   sed.'.PHP_EOL;
        $expected .= '   Fusce egestas congue massa semper cursus. Donec quis pretium tellus.'.PHP_EOL;
        $expected .= '   In lacinia, augue ut ornare porttitor, diam nunc faucibus purus, et accumsan'.PHP_EOL;
        $expected .= '   eros sapien at sem.'.PHP_EOL;
        $expected .= '   Sed pulvinar aliquam malesuada. Aliquam erat volutpat. Mauris gravida rutrum'.PHP_EOL;
        $expected .= '   lectus at egestas'.PHP_EOL.PHP_EOL;
        $expected .= 'Deprecated sniffs are still run, but will stop working at some point in the'.PHP_EOL;
        $expected .= 'future.'.PHP_EOL.PHP_EOL;

        $this->expectOutputString($expected);

        $ruleset->showSniffDeprecations();

    }//end testDeprecatedSniffsWarning()


    /**
     * Test deprecated sniffs are listed alphabetically in the deprecated sniffs warning.
     *
     * This tests the following aspects:
     * 1. That the summary line uses the correct grammar when there is a single deprecated sniff.
     * 2. That the separator line below the summary maximizes at the longest line length.
     * 3. That the word wrapping respects the maximum report width.
     * 4. That the sniff name is truncated if it is longer than the max report width.
     *
     * @param int    $reportWidth    Report width for the test.
     * @param string $expectedOutput Expected output.
     *
     * @dataProvider dataReportWidthIsRespected
     *
     * @return void
     */
    public function testReportWidthIsRespected($reportWidth, $expectedOutput)
    {
        // Set up the ruleset.
        $standard = __DIR__.'/ShowSniffDeprecationsReportWidthTest.xml';
        $config   = new ConfigDouble(['.', "--standard=$standard", "--report-width=$reportWidth", '--no-colors']);
        $ruleset  = new Ruleset($config);

        $this->expectOutputString($expectedOutput);

        $ruleset->showSniffDeprecations();

    }//end testReportWidthIsRespected()


    /**
     * Data provider.
     *
     * @see testReportWidthIsRespected()
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataReportWidthIsRespected()
    {
        $summaryLine = 'WARNING: The SniffDeprecationTest standard uses 1 deprecated sniff'.PHP_EOL;

        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- Test readability is more important.
        return [
            'Report width small: 40; with truncated sniff name and wrapped header and footer lines' => [
                'reportWidth'    => 40,
                'expectedOutput' => 'WARNING: The SniffDeprecationTest'.PHP_EOL
                    .'standard uses 1 deprecated sniff'.PHP_EOL
                    .'----------------------------------------'.PHP_EOL
                    .'-  Fixtures.Deprecated.WithLongRepla...'.PHP_EOL
                    .'   This sniff has been deprecated since'.PHP_EOL
                    .'   v3.8.0 and will be removed in'.PHP_EOL
                    .'   v4.0.0. Lorem ipsum dolor sit amet,'.PHP_EOL
                    .'   consectetur adipiscing elit. Fusce'.PHP_EOL
                    .'   vel vestibulum nunc. Sed luctus'.PHP_EOL
                    .'   dolor tortor, eu euismod purus'.PHP_EOL
                    .'   pretium sed. Fusce egestas congue'.PHP_EOL
                    .'   massa semper cursus. Donec quis'.PHP_EOL
                    .'   pretium tellus. In lacinia, augue ut'.PHP_EOL
                    .'   ornare porttitor, diam nunc faucibus'.PHP_EOL
                    .'   purus, et accumsan eros sapien at'.PHP_EOL
                    .'   sem. Sed pulvinar aliquam malesuada.'.PHP_EOL
                    .'   Aliquam erat volutpat. Mauris'.PHP_EOL
                    .'   gravida rutrum lectus at egestas.'.PHP_EOL
                    .'   Fusce tempus elit in tincidunt'.PHP_EOL
                    .'   dictum. Suspendisse dictum egestas'.PHP_EOL
                    .'   sapien, eget ullamcorper metus'.PHP_EOL
                    .'   elementum semper. Vestibulum sem'.PHP_EOL
                    .'   justo, consectetur ac tincidunt et,'.PHP_EOL
                    .'   finibus eget libero.'.PHP_EOL.PHP_EOL
                    .'Deprecated sniffs are still run, but'.PHP_EOL
                    .'will stop working at some point in the'.PHP_EOL
                    .'future.'.PHP_EOL.PHP_EOL,
            ],
            'Report width default: 80'                                                              => [
                'reportWidth'    => 80,
                'expectedOutput' => $summaryLine.str_repeat('-', 80).PHP_EOL
                    .'-  Fixtures.Deprecated.WithLongReplacement'.PHP_EOL
                    .'   This sniff has been deprecated since v3.8.0 and will be removed in v4.0.0.'.PHP_EOL
                    .'   Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vel'.PHP_EOL
                    .'   vestibulum nunc. Sed luctus dolor tortor, eu euismod purus pretium sed.'.PHP_EOL
                    .'   Fusce egestas congue massa semper cursus. Donec quis pretium tellus. In'.PHP_EOL
                    .'   lacinia, augue ut ornare porttitor, diam nunc faucibus purus, et accumsan'.PHP_EOL
                    .'   eros sapien at sem. Sed pulvinar aliquam malesuada. Aliquam erat volutpat.'.PHP_EOL
                    .'   Mauris gravida rutrum lectus at egestas. Fusce tempus elit in tincidunt'.PHP_EOL
                    .'   dictum. Suspendisse dictum egestas sapien, eget ullamcorper metus elementum'.PHP_EOL
                    .'   semper. Vestibulum sem justo, consectetur ac tincidunt et, finibus eget'.PHP_EOL
                    .'   libero.'.PHP_EOL.PHP_EOL
                    .'Deprecated sniffs are still run, but will stop working at some point in the'.PHP_EOL
                    .'future.'.PHP_EOL.PHP_EOL,
            ],
            'Report width matches longest line: 666; the message should not wrap'                   => [
                // Length = 4 padding + 75 base line + 587 custom message.
                'reportWidth'    => 666,
                'expectedOutput' => $summaryLine.str_repeat('-', 666).PHP_EOL
                    .'-  Fixtures.Deprecated.WithLongReplacement'.PHP_EOL
                    .'   This sniff has been deprecated since v3.8.0 and will be removed in v4.0.0. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vel vestibulum nunc. Sed luctus dolor tortor, eu euismod purus pretium sed. Fusce egestas congue massa semper cursus. Donec quis pretium tellus. In lacinia, augue ut ornare porttitor, diam nunc faucibus purus, et accumsan eros sapien at sem. Sed pulvinar aliquam malesuada. Aliquam erat volutpat. Mauris gravida rutrum lectus at egestas. Fusce tempus elit in tincidunt dictum. Suspendisse dictum egestas sapien, eget ullamcorper metus elementum semper. Vestibulum sem justo, consectetur ac tincidunt et, finibus eget libero.'
                    .PHP_EOL.PHP_EOL
                    .'Deprecated sniffs are still run, but will stop working at some point in the future.'.PHP_EOL.PHP_EOL,
            ],
            'Report width wide: 1000; delimiter line length should match longest line'              => [
                'reportWidth'    => 1000,
                'expectedOutput' => $summaryLine.str_repeat('-', 666).PHP_EOL
                    .'-  Fixtures.Deprecated.WithLongReplacement'.PHP_EOL
                    .'   This sniff has been deprecated since v3.8.0 and will be removed in v4.0.0. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vel vestibulum nunc. Sed luctus dolor tortor, eu euismod purus pretium sed. Fusce egestas congue massa semper cursus. Donec quis pretium tellus. In lacinia, augue ut ornare porttitor, diam nunc faucibus purus, et accumsan eros sapien at sem. Sed pulvinar aliquam malesuada. Aliquam erat volutpat. Mauris gravida rutrum lectus at egestas. Fusce tempus elit in tincidunt dictum. Suspendisse dictum egestas sapien, eget ullamcorper metus elementum semper. Vestibulum sem justo, consectetur ac tincidunt et, finibus eget libero.'
                    .PHP_EOL.PHP_EOL
                    .'Deprecated sniffs are still run, but will stop working at some point in the future.'.PHP_EOL.PHP_EOL,
            ],
        ];
        // phpcs:enable

    }//end dataReportWidthIsRespected()


    /**
     * Test deprecated sniffs are listed alphabetically in the deprecated sniffs warning.
     *
     * Additionally, this test verifies that deprecated sniffs are still registered to run.
     *
     * @return void
     */
    public function testDeprecatedSniffsAreListedAlphabetically()
    {
        // Set up the ruleset.
        $standard = __DIR__.'/ShowSniffDeprecationsOrderTest.xml';
        $config   = new ConfigDouble(["--standard=$standard", '--no-colors']);
        $ruleset  = new Ruleset($config);

        $expected  = 'WARNING: The SniffDeprecationTest standard uses 2 deprecated sniffs'.PHP_EOL;
        $expected .= '--------------------------------------------------------------------------------'.PHP_EOL;
        $expected .= '-  Fixtures.Deprecated.WithoutReplacement'.PHP_EOL;
        $expected .= '   This sniff has been deprecated since v3.4.0 and will be removed in v4.0.0.'.PHP_EOL;
        $expected .= '-  Fixtures.Deprecated.WithReplacement'.PHP_EOL;
        $expected .= '   This sniff has been deprecated since v3.8.0 and will be removed in v4.0.0.'.PHP_EOL;
        $expected .= '   Use the Stnd.Category.OtherSniff sniff instead.'.PHP_EOL.PHP_EOL;
        $expected .= 'Deprecated sniffs are still run, but will stop working at some point in the'.PHP_EOL;
        $expected .= 'future.'.PHP_EOL.PHP_EOL;

        $this->expectOutputString($expected);

        $ruleset->showSniffDeprecations();

        // Verify that the sniffs have been registered to run.
        $this->assertCount(2, $ruleset->sniffCodes, 'Incorrect number of sniff codes registered');
        $this->assertArrayHasKey(
            'Fixtures.Deprecated.WithoutReplacement',
            $ruleset->sniffCodes,
            'WithoutReplacement sniff not registered'
        );
        $this->assertArrayHasKey(
            'Fixtures.Deprecated.WithReplacement',
            $ruleset->sniffCodes,
            'WithReplacement sniff not registered'
        );

    }//end testDeprecatedSniffsAreListedAlphabetically()


    /**
     * Test that an exception is thrown when any of the interface required methods does not
     * comply with the return type/value requirements.
     *
     * @param string $standard         The standard to use for the test.
     * @param string $exceptionMessage The contents of the expected exception message.
     *
     * @dataProvider dataExceptionIsThrownOnIncorrectlyImplementedInterface
     *
     * @return void
     */
    public function testExceptionIsThrownOnIncorrectlyImplementedInterface($standard, $exceptionMessage)
    {
        $exception = 'PHP_CodeSniffer\Exceptions\RuntimeException';
        if (method_exists($this, 'expectException') === true) {
            // PHPUnit 5+.
            $this->expectException($exception);
            $this->expectExceptionMessage($exceptionMessage);
        } else {
            // PHPUnit 4.
            $this->setExpectedException($exception, $exceptionMessage);
        }

        // Set up the ruleset.
        $standard = __DIR__.'/'.$standard;
        $config   = new ConfigDouble(["--standard=$standard"]);
        $ruleset  = new Ruleset($config);

        $ruleset->showSniffDeprecations();

    }//end testExceptionIsThrownOnIncorrectlyImplementedInterface()


    /**
     * Data provider.
     *
     * @see testExceptionIsThrownOnIncorrectlyImplementedInterface()
     *
     * @return array<string, array<string, string>>
     */
    public static function dataExceptionIsThrownOnIncorrectlyImplementedInterface()
    {
        return [
            'getDeprecationVersion() does not return a string' => [
                'standard'         => 'ShowSniffDeprecationsInvalidDeprecationVersionTest.xml',
                'exceptionMessage' => 'The Fixtures\Sniffs\DeprecatedInvalid\InvalidDeprecationVersionSniff::getDeprecationVersion() method must return a non-empty string, received double',
            ],
            'getRemovalVersion() does not return a string'     => [
                'standard'         => 'ShowSniffDeprecationsInvalidRemovalVersionTest.xml',
                'exceptionMessage' => 'The Fixtures\Sniffs\DeprecatedInvalid\InvalidRemovalVersionSniff::getRemovalVersion() method must return a non-empty string, received array',
            ],
            'getDeprecationMessage() does not return a string' => [
                'standard'         => 'ShowSniffDeprecationsInvalidDeprecationMessageTest.xml',
                'exceptionMessage' => 'The Fixtures\Sniffs\DeprecatedInvalid\InvalidDeprecationMessageSniff::getDeprecationMessage() method must return a string, received object',
            ],
            'getDeprecationVersion() returns an empty string'  => [
                'standard'         => 'ShowSniffDeprecationsEmptyDeprecationVersionTest.xml',
                'exceptionMessage' => 'The Fixtures\Sniffs\DeprecatedInvalid\EmptyDeprecationVersionSniff::getDeprecationVersion() method must return a non-empty string, received ""',
            ],
            'getRemovalVersion() returns an empty string'      => [
                'standard'         => 'ShowSniffDeprecationsEmptyRemovalVersionTest.xml',
                'exceptionMessage' => 'The Fixtures\Sniffs\DeprecatedInvalid\EmptyRemovalVersionSniff::getRemovalVersion() method must return a non-empty string, received ""',
            ],
        ];

    }//end dataExceptionIsThrownOnIncorrectlyImplementedInterface()


}//end class
